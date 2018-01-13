<template>
    <el-dialog :title="title" size="large" :visible.sync="dialogFormVisible">
        <el-form :model="item" ref="form" label-width="120px">
            <el-form-item label="Title">
                <el-input v-model="item.title"></el-input>
            </el-form-item>
            <el-form-item label="slug">
                <el-input v-model="item.slug"></el-input>
            </el-form-item>
            <el-form-item label="Content">
                <vue-html5-editor name="article-content" :content="item.content" :height="300" @change="updateContent"></vue-html5-editor>
            </el-form-item>
            <el-form-item label="Draft">
                <el-select v-model="item.status">
                    <el-option value="1" label="Draft"></el-option>
                    <el-option value="0" label="Published"></el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button @click="dialogFormVisible = false">Cancel</el-button>
            <el-button type="primary" @click="save(item)">Save</el-button>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        data(){
            return {
                title: 'Add Article',
                dialogFormVisible: false,
                item:{},
            }
        },
        methods: {
            save(item) {
                let self = this;

                if (item.id) {
                    this.$http.put('admin/articles/' + item.id, item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-articles');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        });
                } else {
                    this.$http.post('admin/articles', item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-articles');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        })
                }
            },
            updateContent(content) {
                this.item.content = content;
            }
        },
        created() {
            let self = this;
            this.$bus.on('edit-article', function(item){
                self.item = JSON.parse(JSON.stringify(item));
                if (item.id) {
                    self.title = 'Edit Article';
                    self.item.status = self.item.status.toString();
                } else {
                    self.item.status = '0';
                }
                self.dialogFormVisible = true;
            })
        }
    }
</script>