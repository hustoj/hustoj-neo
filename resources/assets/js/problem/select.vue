<template>
    <el-dialog title="Choose Problem" v-model="dialogVisible">
        <div class="head-bar">
            <el-form :inline="true" :model="params">
                <el-form-item>
                    <el-input @keyup.enter="search(params)" size="small" v-model="params.id" placeholder="ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter="search(params)" size="small" v-model="params.title" placeholder="title"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter="search(params)" size="small" v-model="params.source" placeholder="Source"></el-input>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
            </el-form>
        </div>
        <el-table v-loading="loading" :data="tableData" style="width: 100%">
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="title" label="Title" width="400"></el-table-column>
            <el-table-column prop="source" label="Source" width="400"></el-table-column>
            <el-table-column label="">
                <template #default="scope">
                    <el-button type="primary" plain :icon="Check" @click="handleSelect(scope.row)"></el-button>
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
    </el-dialog>
</template>

<script>
    import { Check } from '@element-plus/icons-vue';
    export default {
        setup() {
            return { Check };
        },
        data() {
            return {
                loading: false,
                tableData: null,
                total: 0,
                dialogVisible: false,
                params:{
                    per_page: 20,
                    page: 1
                }
            }
        },
        created() {
            let self = this;
            this.$bus.on('select-problem', function(){
                self.dialogVisible = true;
            })
        },
        methods: {
            search() {
                this.loadData();
            },
            handleSelect(item){
                this.dialogVisible = false;
                this.$bus.emit('problem-selected', item);
            },
            loadData(){
                this.loading = true;
                this.$http.get('/admin/problems', {
                    params: this.params
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
            handleSizeChange(pageSize){
                this.params.per_page = pageSize;
                this.loadData();
            },
            handleCurrentChange(){
                this.loadData();
            }
        }
    }
</script>
