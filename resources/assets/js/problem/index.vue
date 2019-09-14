<template>
    <div>
        <div class="search-bar">
            <el-form :inline="true" :model="params">
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.id" placeholder="ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.title" placeholder="Title"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.source" placeholder="Source"></el-input>
                </el-form-item>
                <el-form-item label="Status">
                    <el-select v-model="params.status" placeholder="All" >
                        <el-option label="All" value=""></el-option>
                        <el-option label="Enabled" value="0"></el-option>
                        <el-option label="Disabled" value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
                <el-button type="success" plain @click="handleAdd()">Add</el-button>
            </el-form>
        </div>
        <el-table v-loading.body="loading" :data="tableData" style="width: 100%" size="medium">
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="title" label="Title" width="400"></el-table-column>
            <el-table-column prop="source" label="Source" width="400"></el-table-column>
            <el-table-column prop="updated_at" label="Updated At" width="180"></el-table-column>
            <el-table-column>
                <template slot-scope="scope">
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
        <problem-edit></problem-edit>
    </div>
</template>

<script>
    import service from "../modules/problem";
    import problemEdit from "./edit.vue";

    export default {
        components: {
            problemEdit
        },
        data() {
            return {
                loading: false,
                tableData: [],
                total: 0,
                params:{
                    per_page: 20,
                    page: 1
                },
                base_path: '/admin/problems/',
            }
        },
        created() {
            let self = this;
            this.loadData();
            this.$bus.on('reload-problems', function(){
                self.loadData();
            })
        },
        watch: {
            '$route': 'loadData',
        },
        methods: {
            search() {
                this.loadData();
            },
            handleEdit(item){
                this.$bus.emit('edit-problem', item);
            },
            handleAdd() {
                this.$bus.emit('edit-problem', {});
            },
            handleDelete(item){
                let self = this;

                let message = `Did you want delete [${item.title}] ?`;
                this.$confirm(message, 'Alert', {type: 'warning'})
                    .then(function () {
                        self.$http.delete(self.base_path + item.id)
                            .then(function (resp) {
                                self.$message.success('Delete Done!');
                                self.loadData()
                            });
                    })
            },
            loadData(){
                this.loading = true;
                this.$http.get(this.base_path, {
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
                            type: alert,
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
