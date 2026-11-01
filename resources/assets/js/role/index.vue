<template>
    <div>
        <div class="search-bar">
            <el-form :inline="true" :model="params" @submit.prevent>
                <el-form-item>
                    <el-input @keyup.enter="search(params)" v-model="params.name" placeholder="Role Name"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" plain @click="search(params)">Search</el-button>
                    <el-button type="success" plain @click="handleAdd()">Add</el-button>
                </el-form-item>
            </el-form>
        </div>
        <el-table size="default" v-loading="loading" :data="tableData" style="width: 100%">
            <el-table-column prop="id" label="Role ID" width="80"></el-table-column>
            <el-table-column prop="name" label="Role Name" width="180"></el-table-column>
            <el-table-column prop="display_name" label="Display Name" width="180"></el-table-column>
            <el-table-column prop="description" label="Description" width="180"></el-table-column>
            <el-table-column label="">
                <template #default="scope">
                    <el-button type="primary" circle size="small" :icon="Edit" @click="handleEdit(scope.row)"></el-button>
                    <el-button type="danger" circle size="small" :icon="Delete" @click="handleDelete(scope.row)"></el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                v-model:current-page="params.page"
                :page-sizes="[20, 50, 100]"
                :page-size="params.per_page"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
        </el-pagination>
        <role-edit></role-edit>
    </div>
</template>

<script>
    import RoleEdit from './edit.vue';
    import { Edit, Delete } from '@element-plus/icons-vue';

    export default {
        setup() {
            return { Edit, Delete };
        },
        components: {
            RoleEdit
        },
        data() {
            return {
                params: {
                    per_page: 20,
                    page: 1
                },
                loading: false,
                tableData: null,
                total: 0,
            }
        },
        created() {
            this.loadData();
            let self = this;
            this.$bus.on('reload-roles', function () {
                self.loadData();
            })
        },
        watch: {
            '$route': 'loadData',
        },
        methods: {
            handleClose(done) {
                this.$confirm('sure ?')
                    .then(_ => {
                        done();
                    })
                    .catch(_ => {
                    });
            },
            handleEdit(item) {
                this.$bus.emit('edit-role', item);
            },
            handleAdd() {
                this.$bus.emit('edit-role', {});
            },
            search(params) {
                this.loadData();
            },
            handleDelete(item) {
                let self = this;
                let message = 'Will delete role (' + item.display_name +'), are you sure?';
                this.$confirm(message, 'Alert', {type: 'warning'})
                    .then(() => {
                    self.$http.delete('/admin/roles/' + item.id)
                        .then(() => {
                            self.$message.success({
                                showClose: true,
                                message: 'Delete Done!'
                            });
                            self.loadData();
                        }).catch((resp) => {
                            self.$message.error({
                                showClose: true,
                                message: resp.message,
                            })
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Cancelled'
                    });
                });
            },
            loadData(params) {
                if (params === undefined) {
                    params = this.params;
                }
                this.loading = true;
                this.$http.get('/admin/roles', {
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
                            type: 'warning',
                        })
                    }
                }.bind(this));
            },
            saveData(item) {
                this.loading = true;
                this.$http.put('/admin/roles/' + item.id, {});
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
