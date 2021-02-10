<template>
    <div>
        <div class="search-bar">
            <el-form :inline="true" :model="params">
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.id" placeholder="ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.name" placeholder="Account"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.email" placeholder="email"></el-input>
                </el-form-item>
                <el-form-item label="Status">
                    <el-select v-model="params.status" placeholder="All">
                        <el-option label="All" value=""></el-option>
                        <el-option label="Enabled" value="0"></el-option>
                        <el-option label="Disabled" value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
                <el-button type="success" plain @click="handleAdd()">Add</el-button>
                <el-button type="danger" plain @click="batchRemoveUser()">Remove Selected Users</el-button>
            </el-form>
        </div>
        <el-table size="medium" v-loading.body="loading"
                  :data="tableData"
                  @selection-change="handleSelectionChange"
                  :row-class-name="tableRowClassName"
                  style="width: 100%">
            <el-table-column
                type="selection"
                width="55">
            </el-table-column>
            <el-table-column prop="id" label="ID" width="100"></el-table-column>
            <el-table-column prop="username" label="Account" width="160"></el-table-column>
            <el-table-column prop="nick" label="Nick" width="180"></el-table-column>
            <el-table-column label="E-mail" width="240">
                <template slot-scope="scope">
                    <el-tooltip placement="top">
                        <div slot="content" v-if="scope.row.email_verified_at">{{ scope.row.email_verified_at }}</div>
                        <div slot="content" v-if="!scope.row.email_verified_at">not verified</div>
                        <el-button size="small" v-if="scope.row.email_verified_at" type="success" plain>{{ scope.row.email }}</el-button>
                        <el-button size="small" v-if="!scope.row.email_verified_at" plain>{{ scope.row.email }}</el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
            <el-table-column label="Submit/Accepted" width="150">
                <template slot-scope="scope">
                    {{ scope.row.submit }} / {{ scope.row.solved }}
                </template>
            </el-table-column>
            <el-table-column prop="created_at" label="Registered At" width="160"></el-table-column>
            <el-table-column prop="last_access_at" label="Last Access at" width="200">
                <template slot-scope="scope">
                    <el-tooltip content="show more" placement="top">
                        <el-button @click="showRecentLogging(scope.row)" type="text">{{ scope.row.access_at }} </el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
            <el-table-column>
                <template slot-scope="scope">
                    <el-button type="warning" plain size="mini" @click="toggleStatus(scope.row.id, scope.row.status)">
                        {{ scope.row.status | showStatusBtn }}
                    </el-button>
                    <el-button type="primary" circle size="mini" icon="el-icon-edit" @click="handleEdit(scope.row)"></el-button>
                    <el-button type="danger" circle size="mini" icon="el-icon-delete" @click="handleDelete(scope.row)"></el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page.sync="params.page"
                :page-sizes="[20, 50, 100]"
                :page-size="params.per_page"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
        </el-pagination>

        <user-edit></user-edit>
        <logging-records></logging-records>
    </div>
</template>
<style scoped>
    .el-table >>> .positive-row {
        background: oldlace;
    }

    .el-table >>> .success-row {
        background: #f0f9eb;
    }
</style>
<script>
    import UserEdit from './edit.vue';
    import LoggingRecords from './logging.vue';

    import {showStatusBtn} from '../filter';

    export default {
        components: {
            UserEdit,
            LoggingRecords
        },
        data() {
            return {
                loading: false,
                tableData: null,
                total: 0,
                params: {
                    per_page: 20,
                    page: 1
                },
                multipleSelection: []
            }
        },
        created() {
            this.loadData();
            let self = this;
            this.$bus.on('reload-users', function () {
                self.loadData();
            })
        },
        watch: {
            '$route': 'loadData',
        },
        methods: {
            tableRowClassName(params) {
                let user = params.row;

                if(user.status) {
                    return 'positive-row';
                }
                if (user.submit > 0) {
                    return 'success-row';
                }
                return '';
            },
            batchRemoveUser() {
                if (this.multipleSelection.length === 0) {
                    this.$message("no user selected!");
                    return;
                }
                console.log("batch remove " + this.multipleSelection.length + " users!");
                let self = this;
                let total_removed = 0;
                this.multipleSelection.map(function (user) {
                    if (user.submit > 0) {
                        let message = "user [" + user.username + "] has submit solutions, will skip batch delete!";
                        console.log('batchRemoveUser', message)
                        self.$notify({
                            title: 'warn',
                            message: message,
                            type: 'warning'
                        });
                        return;
                    }

                    self.doDeleteUser(user).then(function (resp) {
                        console.log("user " + user.username + " is deleted!")
                    });
                    total_removed++;
                })
                if (total_removed > 0) {
                    self.$message({
                        message: 'total ' + total_removed + ' users removed',
                        type: 'success'
                    })
                } else {
                    self.$message({
                        message: 'no user removed',
                        type: 'success'
                    })
                }

                self.loadData();
            },
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            showRecentLogging(user) {
                this.$bus.emit('view-user-login-log', user.id);
            },
            search(params) {
                this.loadData();
            },
            handleEdit(item) {
                this.$bus.emit('edit-user', item);
            },
            handleAdd() {
                this.$bus.emit('edit-user', {});
            },
            toggleStatus(id, status) {
                let self = this;
                let data = {};
                if (status === 1) {
                    data.status = 0;
                } else {
                    data.status = 1;
                }
                this.$http.put('/admin/users/' + id, data)
                    .then(() => {
                        self.$message.success({
                            showClose: true,
                            message: 'Update success!'
                        });
                        self.loadData();
                    }).catch(() => {
                        self.$message.success({
                            showClose: true,
                            message: 'Update failed'
                        });
                });
            },
            doDeleteUser(user) {
                return this.$http.delete('/admin/users/' + user.id);
            },
            handleDelete(item) {
                let self = this;
                let message = 'Will delete (' + item.username + '), are you sure?';
                this.$confirm(message, 'notice', {type: 'warning'})
                    .then(() => {
                        this.doDeleteUser(item)
                        .then(() => {
                            self.$message.success('Delete Done!');
                            self.loadData();
                        }).catch((resp) => {
                            self.$message.error(resp)
                        });
                }).catch(() => {
                    this.$message.info('Cancelled');
                });
            },
            loadData(params) {
                if (params === undefined) {
                    params = this.params;
                }
                this.loading = true;
                this.$http.get('/admin/users', {
                    params: params
                }).then(function (response) {
                    this.loading = false;
                    if (response && response.status === 200) {
                        this.tableData = response.data.data;
                        this.total = response.data.total;
                        document.body.scrollTop = 0;
                    } else {
                        this.$message({
                            showClose: true,
                            message: response.statusText,
                            type: alert,
                        })
                    }
                }.bind(this));
            },
            handleSizeChange(pageSize) {
                this.params.per_page = pageSize;
                this.loadData();
            },
            handleCurrentChange() {
                this.loadData();
            }
        }
    }
</script>
