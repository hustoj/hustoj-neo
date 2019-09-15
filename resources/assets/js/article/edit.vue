<template>
    <el-dialog width="80%" :title="title" size="large" :visible.sync="dialogFormVisible">
        <el-form :rules="rules" :model="item" label-width="120px" ref="articleEditForm">
            <el-form-item label="Title" prop="title">
                <el-input v-model="item.title"></el-input>
            </el-form-item>
            <el-form-item label="slug" prop="slug">
                <el-input v-model="item.slug"></el-input>
            </el-form-item>
            <el-form-item label="Content" prop="content">
                <vue-html5-editor name="article-content" :content="item.content" :height="300" @change="updateContent"></vue-html5-editor>
            </el-form-item>
            <el-form-item label="Draft" prop="status">
                <el-select v-model="item.status">
                    <el-option value="0" label="Draft"></el-option>
                    <el-option value="1" label="Published"></el-option>
                </el-select>
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
                title: 'Add Article',
                dialogFormVisible: false,
                item:{
                    title: '',
                    slug: '',
                    content: '',
                    disable: 0
                },
                rules: {
                    title: [
                        { required: true, message: 'please input title', trigger: 'blur' }
                    ],
                    slug: [
                        { required: true, message: 'please input slug', trigger: 'blur' }
                    ],
                    content: [
                        { required: true, message: 'please input content', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            save(item) {
                let self = this;

                this.$refs['articleEditForm'].validate().then(function(){
                    if (item.id) {
                        self.$http.put('admin/articles/' + item.id, item)
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
                }).catch(function(resp) {
                    self.$message.error('Miss some required fields');
                    return false;
                });
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
