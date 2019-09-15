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
                    <el-select v-model="params.status" placeholder="All">
                        <el-option label="All" value=""></el-option>
                        <el-option label="Draft" value="0"></el-option>
                        <el-option label="Published" value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
                <el-button type="success" plain @click="handleAdd()">Add</el-button>
            </el-form>
        </div>
        <el-table v-loading.body="loading" :data="tableData" style="width: 100%" size="medium">
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="title" label="Title" width="600"></el-table-column>
            <el-table-column prop="status" label="Draft" width="100">
                <template slot-scope="scope">
                    {{ scope.row.status == '1' ? 'Published' : 'Draft'}}
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
        <article-edit></article-edit>
    </div>
</template>

<script>
    import ArticleEdit from "./edit.vue";

    export default {
        components: {
            ArticleEdit
        },
        data() {
            return {
                loading: false,
                tableData: null,
                params: {
                    page: 1,
                    per_page: 20
                },
                total: 0,
                base_path: '/admin/articles/',
            }
        },
        created() {
            this.loadData();
            let self = this;

            this.$bus.on('reload-articles', function () {
                self.loadData();
            })
        },
        watch: {
            '$route': 'loadData',
        },
        methods: {
            handleEdit(item) {
                this.$bus.emit('edit-article', item);
            },
            handleAdd() {
                this.$bus.emit('edit-article', {});
            },
            handleDelete(item) {
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
            search(params) {
                this.loadData();
            },
            loadData() {
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
                        this.$message.warning(response.status)
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
