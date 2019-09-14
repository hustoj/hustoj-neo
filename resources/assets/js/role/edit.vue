<template>
    <el-dialog width="80%" :title="title" :visible.sync="dialogFormVisible">
        <el-form :model="role">
            <el-form-item label="Role Name">
                <el-input v-model="role.name"></el-input>
            </el-form-item>
            <el-form-item label="Display Name">
                <el-input v-model="role.display_name"></el-input>
            </el-form-item>
            <el-form-item label="Description">
                <el-input type="textarea" v-model="role.description"></el-input>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button plain @click="dialogFormVisible = false">Cancel</el-button>
            <el-button type="primary" plain @click="save(role)">Save</el-button>
        </div>
    </el-dialog>
</template>

<script>

    export default {
        data(){
            return {
                dialogFormVisible: false,
                role:{},
                title: 'Add Role',
            }
        },
        methods: {
            save(item) {
                let self = this;

                if (item.id) {
                    this.$http.put('admin/roles/' + item.id, item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-roles');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        });
                } else {
                    this.$http.post('admin/roles', item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-roles');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        })
                }
            }
        },
        created() {
            let self = this;
            this.$bus.on('edit-role', function(role){
                self.role = JSON.parse(JSON.stringify(role));
                if (role.id) {
                    self.title = 'Edit Role';
                }
                self.dialogFormVisible = true;
            })
        }
    }
</script>
