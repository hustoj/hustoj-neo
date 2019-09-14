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
                <el-form-item label="Status">
                    <el-select v-model="params.private" placeholder="All">
                        <el-option label="All" value=""></el-option>
                        <el-option label="Private" value="1"></el-option>
                        <el-option label="Public" value="0"></el-option>
                    </el-select>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
                <el-button type="success" plain @click="handleAdd()">Add</el-button>
            </el-form>
        </div>
        <el-table size="medium" v-loading.body="loading" :data="tableData" style="width: 100%">
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="title" label="Title" width="600"></el-table-column>
            <el-table-column label="Private" width="100">
                <template slot-scope="scope">
                    {{ scope.row.private | privateStatus}}
                </template>
            </el-table-column>
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
        <contest-edit></contest-edit>
    </div>
</template>

<script>
    import ContestEdit from "./edit.vue";
    import {privateStatus} from "../filter";

    export default {
        components: {
            ContestEdit
        },
        data() {
            return {
                loading: false,
                tableData: null,
                total: 0,
                params: {
                    page: 1,
                    per_page: 20,
                }
            }
        },
        created() {
            this.loadData();

            let self = this;

            this.$bus.on('reload-contests', function () {
                self.loadData();
            })
        },
        watch: {
            '$route': 'loadData',
        },
        methods: {
            search(params) {
                this.loadData();
            },
            handleEdit(item){
                this.$bus.emit('edit-contest', item);
            },
            handleAdd() {
                this.$bus.emit('edit-contest', {});
            },
            handleDelete(item){
                let self = this;
                let message = `Will delete contest ${item.title}, are you sure ?`;
                this.$confirm(message, 'Alert', {type: 'warning'})
                    .then(function () {
                        self.$http.delete(`/admin/contests/${item.id}`)
                            .then(function (resp) {
                                self.$message.success('Delete Done!');
                                self.loadData();
                            })
                    })
            },
            loadData(){
                this.loading = true;
                this.$http.get('/admin/contests', {
                    params: this.params
                }).then(function (response) {
                    this.loading = false;
                    if (response && response.status === 200) {
                        this.tableData = response.data.data;
                        this.total = response.data.total;
                        document.body.scrollTop = 0;
                    } else {
                        this.$message.error('load failed!');
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
