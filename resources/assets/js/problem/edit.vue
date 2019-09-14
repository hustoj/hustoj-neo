<template>
    <el-dialog width="80%" :title="title" size="large" :visible.sync="dialogFormVisible">
        <el-form :model="item" ref="form" label-width="120px">
            <el-tabs type="border-card">
                <el-tab-pane label="Basic">
                    <el-form-item label="Title">
                        <el-input v-model="item.title"></el-input>
                    </el-form-item>
                    <el-row>
                        <el-col :span="6">
                            <el-form-item>
                                <el-checkbox v-model="spj" @change="changeSpj($event)">Special Judge</el-checkbox>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Time Limit">
                                <el-input v-model="item.time_limit" placeholder="time limit">
                                    <template slot="append">s</template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Memory Limit">
                                <el-input v-model="item.memory_limit" placeholder="memory limit">
                                    <template slot="append">MB</template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-form-item label="Description">
                        <vue-html5-editor name="problem-description" :content="item.description" :height="160"
                                          @change="updateDescription"></vue-html5-editor>
                    </el-form-item>
                    <el-form-item label="Input">
                        <vue-html5-editor name="problem-input" :content="item.input" :height="120"
                                          @change="updateInput"></vue-html5-editor>
                    </el-form-item>
                    <el-form-item label="Output">
                        <vue-html5-editor name="problem-output" :content="item.output" :height="120"
                                          @change="updateOutput"></vue-html5-editor>
                    </el-form-item>
                    <el-form-item label="Hint">
                        <el-input type="textarea" v-model="item.hint"></el-input>
                    </el-form-item>
                    <el-form-item label="Source">
                        <el-input type="textarea" v-model="item.source"></el-input>
                    </el-form-item>
                </el-tab-pane>
                <el-tab-pane label="Data">
                    <el-form-item label="Sample Input">
                        <el-input type="textarea" v-model="item.sample_input"
                                  :autosize="{ minRows: 4, maxRows: 12}"></el-input>
                    </el-form-item>
                    <el-form-item label="Sample Output">
                        <el-input type="textarea" v-model="item.sample_output"
                                  :autosize="{ minRows: 4, maxRows: 12}"></el-input>
                    </el-form-item>
                    <el-form-item label="Judge Data" v-if="item.id">
                        <el-row :gutter=24>
                            <el-col :span=10>
                                <el-upload
                                        class="upload-data"
                                        ref="upload"
                                        :with-credentials="withCredentials"
                                        :headers="attachedHeaders"
                                        name="files"
                                        :before-upload="beforeFilesUpload"
                                        :action="uploadPath"
                                        :on-success="handleSuccess"
                                        :on-remove="handleRemove"
                                        :file-list="fileList"
                                        :auto-upload="false" style="width: 300px">
                                    <el-button slot="trigger" plain type="primary">Choose</el-button>
                                    <el-button style="margin-left: 10px;" plain type="success"
                                               @click="submitUpload">Upload
                                    </el-button>
                                    <div slot="tip" class="el-upload__tip">
                                        support .in and .out files, take care of file size!
                                    </div>
                                </el-upload>
                            </el-col>
                            <el-col :span=10>
                                <el-card class="box-card">
                                    <div slot="header" class="clearfix">
                                        <span style="line-height: 18px;">Data File List</span>
                                    </div>
                                    <ul v-for="file in currentFileList" class="el-upload-list el-upload-list--text">
                                        <li class="el-upload-list__item is-ready">
                                            <!----><a class="el-upload-list__item-name" :href="accessFileUrl(file)">
                                            <i class="el-icon-document"></i>{{ file }}
                                        </a><label class="el-upload-list__item-status-label"><i
                                                class="el-icon-upload-success el-icon-circle-check"></i></label><i
                                                class="el-icon-close" @click="removeFile(file)"></i><!----><!----></li>
                                    </ul>
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button plain @click="dialogFormVisible = false">Cancel</el-button>
            <el-button type="primary" plain @click="save(item)">Save</el-button>
        </div>
    </el-dialog>
</template>

<script>

    export default {
        data() {
            return {
                dialogFormVisible: false,
                item: {},
                uploadPath: '',
                fileList: [],
                spj: false,
                withCredentials: true,
                attachedHeaders: {},
                currentFileList: [],
                title: 'Add Problem',
            }
        },
        methods: {
            updateDescription(content) {
                this.item.description = content;
                console.log(this.item.description);
            },
            updateInput(content) {
                this.item.input = content;
            },
            updateOutput(content) {
                this.item.output = content;
            },
            save(item) {
                let self = this;
                if (item.id) {
                    this.$http.put('admin/problems/' + item.id, item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-problems');
                        }).catch(function (resp) {
                        self.$message.error('Save failed!');
                    });
                } else {
                    this.$http.post('admin/problems', item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-problems');
                        }).catch(function (resp) {
                        self.$message.error('Save failed!');
                    })
                }
            },
            handleRemove(file, fileList) {
                console.log('remove', file, fileList);
            },
            handleSuccess(response, file, fileList) {
                let index = fileList.indexOf(file);
                if (index !== -1) {
                    fileList.splice(index, 1);
                }
                this.loadDataFiles();
            },
            submitUpload() {
                this.$refs.upload.submit();
            },
            changeDescription(content) {
                this.item.description = content;
            },
            changeInput(content) {
                this.item.input = content;
            },
            changeOutput(content) {
                this.item.output = content;
            },
            changeSpj() {
                item.spj = 0;
                if (this.spj) {
                    item.spj = 1;
                }
            },
            beforeFilesUpload(file) {
                this.attachedHeaders['X-XSRF-TOKEN'] = this.$cookie.get('XSRF-TOKEN');
            },
            removeFile(file) {
                let self = this;
                let message = 'Will delete (' + file + ') from server forever, are you sure ?';
                this.$confirm(message, 'Alert')
                    .then(function () {
                        self.$http.delete(self.accessFileUrl(file))
                            .then(function () {
                                self.$message.success({message: 'delete done'});
                                self.loadDataFiles();
                            })
                    }).catch(function () {

                });
            },
            accessFileUrl(file) {
                if (this.item) {
                    return '/admin/problems/' + this.item.id + '/files/' + file;
                }
            },
            loadDataFiles() {
                let self = this;
                this.$http.get('/admin/problems/' + this.item.id + '/files', {})
                    .then(function (response) {
                        self.currentFileList = response.data;
                    });
            }
        },
        created() {
            let self = this;
            this.$bus.on('edit-problem', function (item) {
                self.item = JSON.parse(JSON.stringify(item));
                if (item.id) {
                    self.title = 'Edit Problem';
                    self.uploadPath = '/admin/problems/' + item.id + '/upload';
                    self.loadDataFiles();
                }

                self.spj = item.spj == 1;

                self.dialogFormVisible = true;
            })
        }
    }
</script>
