<template>
    <el-dialog width="80%" :title="title" size="small" :visible.sync="dialogFormVisible">
        <el-form :model="item" ref="form" label-width="120px">
            <el-form-item label="Key">
                <el-input v-model="item.key"></el-input>
            </el-form-item>
            <el-form-item label="Category">
                <el-input v-model="item.category"></el-input>
            </el-form-item>
            <el-form-item label="Description">
                <el-input v-model="item.description"></el-input>
            </el-form-item>
            <el-form-item label="Value">
                <el-input v-model="item.value"></el-input>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button plain @click="dialogFormVisible = false">Cancel</el-button>
            <el-button type="primary" plain @click="save(item)">Save</el-button>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        data(){
            return {
                title: 'Add Option',
                dialogFormVisible: false,
                item:{},
            }
        },
        methods: {
            save(item) {
                let self = this;

                if (item.id) {
                    this.$http.put('admin/options/' + item.id, item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-options');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        });
                } else {
                    this.$http.post('admin/options', item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-options');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        })
                }
            },
        },
        created() {
            let self = this;
            this.$bus.on('edit-option', function(item){
                self.item = JSON.parse(JSON.stringify(item));
                if (self.item.id) {
                    self.title = 'Edit Option';
                }
                self.dialogFormVisible = true;
            });
        }
    }
</script>
