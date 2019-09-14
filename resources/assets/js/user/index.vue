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
            </el-form>
        </div>
        <el-table size="medium" v-loading.body="loading" :data="tableData" :row-class-name="tableRowClassName" style="width: 100%">
            <el-table-column prop="id" label="ID" width="100"></el-table-column>
            <el-table-column prop="username" label="Account" width="160"></el-table-column>
            <el-table-column prop="nick" label="Nick" width="180"></el-table-column>
            <el-table-column prop="email" label="E-mail" width="200"></el-table-column>
            <el-table-column prop="access.created_at" label="Last Access at" width="160"></el-table-column>
            <el-table-column prop="access.ip" label="Last Access IP" width="160"></el-table-column>
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
    </div>
</template>
<style>
    .el-table .positive-row {
        background: #e2f0e4;
    }
</style>
<script>
    import UserEdit from './edit.vue';
    import {showStatusBtn} from '../filter';

    export default {
        components: {
            UserEdit
        },
        data() {
            return {
                loading: false,
                tableData: null,
                total: 0,
                params: {
                    per_page: 20,
                    page: 1
                }
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
            tableRowClassName(row, index) {
                if(row.status) {
                    return 'positive-row';
                }
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
                if (status == 1) {
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
            handleDelete(item) {
                let self = this;
                let message = 'Will delete (' + item.username + '), are you sure?';
                this.$confirm(message, 'notice', {type: 'warning'})
                    .then(() => {
                    self.$http.delete('/admin/users/' + item.id)
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
