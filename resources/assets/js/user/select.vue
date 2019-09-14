<template>
    <el-dialog title="Choose User" :visible.sync="dialogVisible" size="large">
        <div class="head-bar">
            <el-form :inline="true" :model="params">
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" size="small" v-model="params.id" placeholder="ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" size="small" v-model="params.name" placeholder="Name"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" size="small" v-model="params.email" placeholder="email"></el-input>
                </el-form-item>
                <el-form-item label="Status">
                    <el-select v-model="params.status" placeholder="All">
                        <el-option label="All" value="0"></el-option>
                        <el-option label="Enabled" value="0"></el-option>
                        <el-option label="Disabled" value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
            </el-form>
        </div>
        <el-table v-loading.body="loading" :data="tableData" :row-class-name="tableRowClassName" style="width: 100%">
            <el-table-column prop="id" label="ID" width="200"></el-table-column>
            <el-table-column prop="username" label="Name" width="200"></el-table-column>
            <el-table-column prop="nick" label="Nick" width="300"></el-table-column>
            <el-table-column prop="school" label="School" width="240"></el-table-column>
            <el-table-column label="">
                <template slot-scope="scope">
                    <el-button type="primary" plain icon="check" @click="handleSelect(scope.row)"></el-button>
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
    </el-dialog>
</template>
<style>
    .el-table .positive-row {
        background: #e2f0e4;
    }
</style>
<script>
    export default {
        data() {
            return {
                loading: false,
                tableData: null,
                total: 0,
                dialogVisible: false,
                params: {
                    per_page: 20,
                    page: 1
                }
            }
        },
        created() {
            let self = this;
            this.$bus.on('select-user', function(){
                self.dialogVisible = true;
            })
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
            handleSelect(item){
                this.dialogVisible = false;
                this.$bus.emit('user-selected', item);
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
