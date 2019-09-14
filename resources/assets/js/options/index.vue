<template>
    <div>
        <div class="search-bar">
            <el-form :inline="true" :model="params">
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.key" placeholder="Key"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input @keyup.enter.native="search(params)" v-model="params.category" placeholder="Category"></el-input>
                </el-form-item>
                <el-button type="primary" plain @click="search(params)">Search</el-button>
                <el-button type="success" plain @click="handleAdd()">Add</el-button>
            </el-form>
        </div>
        <el-table size="medium" v-loading.body="loading" :data="tableData" style="width: 100%">
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="key" label="Key" width="100"></el-table-column>
            <el-table-column prop="category" label="Category" width="100"></el-table-column>
            <el-table-column prop="description" label="Description" width="200"></el-table-column>
            <el-table-column prop="value" label="Value" width="100"></el-table-column>
            <el-table-column prop="updated_at" label="Updated At" width="160"></el-table-column>
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
        <option-edit></option-edit>
    </div>
</template>

<script>
    import optionEdit from './edit';

    export default {
        components: {
            optionEdit
        },
        data() {
            return {
                loading: false,
                total: 0,
                params: {
                    key: null,
                    category: null,
                    per_page: 20,
                    page: 1
                },
                tableData: [],
            }
        },
        created() {
            let self = this;
            this.loadData();
            this.$bus.on('reload-options', function(){
                self.loadData();
            })
        },
        methods: {
            search() {
                this.loadData();
            },
            loadData() {
                this.loading = true;
                this.$http.get('/admin/options', {
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
            handleEdit(item) {
                this.$bus.emit('edit-option', item);
            },
            handleAdd() {
                this.$bus.emit('edit-option', {});
            },
            handleDelete(item){
                let self = this;
                let message = `Will remove option ${item.key} from server, sure?`;
                this.$confirm(message, 'Alert', {type: 'warning'})
                    .then(function () {
                        self.$http.delete('/admin/options/' + item.id)
                            .then(function (resp) {
                                self.loadData();
                            }).catch(function (resp) {
                                console.log(resp);
                                self.loadData();
                            })
                    });
            },
            handleSizeChange(pageSize){
                this.params.per_page = pageSize;
                this.loadData();
            },
            handleCurrentChange(page){
                this.loadData();
            }
        }
    }
</script>
