<template>
    <el-dialog width="80%" :title="title" :visible.sync="dialogFormVisible">
        <el-table
            :data="loginRecords"
            style="width: 100%"
            :row-class-name="rowClassName"
        >
            <el-table-column prop="created_at" label="Login at" width="180">
            </el-table-column>
            <el-table-column label="Status" width="180">
                <template slot-scope="scope">
                    <span v-if="scope.row.status == 0">PASS</span>
                    <span v-if="scope.row.status == 1">FAILED</span>
                </template>
            </el-table-column>
            <el-table-column prop="ip" label="IP Address">
            </el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogFormVisible = false" type="primary" plain size="mini">Close</el-button>
        </span>
    </el-dialog>
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
export default {
    data() {
        return {
            title: 'User Logging',
            dialogFormVisible: false,
            loginRecords: [],
        }
    },
    methods: {
        loadData(id) {
            let self = this;
            let params = {
                user_id: id,
                per_page: 20
            }
            this.$http.get('admin/user/logging', {
                params: params
            })
                .then((resp) => {
                    console.log(resp)
                    self.loginRecords = resp.data.data;
                    self.dialogFormVisible = true;
                }).catch(() => {
                    self.$message.success({
                        showClose: true,
                        message: 'Get login records failed!'
                    });
            });
        },
        rowClassName(rowItem) {
            if (rowItem.row.status === 0) {
                return 'success-row';
            }
            return 'positive-row';
        }
    },
    created() {
        let self = this;
        this.$bus.on('view-user-login-log', function (id) {
            self.loadData(id)
        });
    }
}
</script>
