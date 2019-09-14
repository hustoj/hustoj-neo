<template>
    <div>
        <el-dialog width="80%" :title="title" size="large" :visible.sync="dialogFormVisible">
            <el-form ref="form" :model="item" label-width="90px">
                <el-form-item label="Title">
                    <el-input v-model="item.title"></el-input>
                </el-form-item>
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="Start Time">
                            <el-date-picker
                                    v-model="start_time"
                                    type="datetime"
                                    format="yyyy-MM-dd HH:mm:ss"
                                    placeholder="Choose Contest Start Time">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="End Time">
                            <el-date-picker
                                    v-model="end_time"
                                    type="datetime"
                                    format="yyyy-MM-dd HH:mm:ss"
                                    placeholder="Choose Contest End Time">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item>
                    <el-checkbox v-model="private" @change="changePrivate($event)">Private</el-checkbox>
                </el-form-item>
                <el-form-item label="Description">
                    <vue-html5-editor name="contest.description" :content="item.description" :height="120" @change="updateDescription"></vue-html5-editor>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" plain icon="el-icon-plus" @click="addProblem()">Add Problem</el-button>
                    <el-button v-if="private" type="primary" plain icon="el-icon-plus" @click="addUser()">Add User</el-button>
                </el-form-item>
                <el-tabs type="border-card" :value="activePane">
                    <el-tab-pane label="Problem" name="problemPane" size="small">
                        <el-table :data="problems" style="width: 100%">
                            <el-table-column prop="id" label="ID" width="80"></el-table-column>
                            <el-table-column prop="title" label="Title" width="300"></el-table-column>
                            <el-table-column>
                                <template slot-scope="scope">
                                    <el-button type="danger" size="mini" plain icon="el-icon-delete" @click="handleDelete(scope.row, scope.$index)"></el-button>
                                    <el-button type="primary" size="mini"  plain icon="el-icon-arrow-up" @click="handleUp(scope.row, scope.$index)" v-if="scope.$index != 0"></el-button>
                                    <el-button type="primary" size="mini"  plain icon="el-icon-arrow-down" @click="handleDown(scope.row, scope.$index)" v-if="scope.$index != problems.length - 1"></el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-tab-pane>
                    <el-tab-pane label="Users" v-if="private" name="userPane" size="small">
                        <el-table :data="users" style="width: 100%">
                            <el-table-column prop="id" label="ID" width="120"></el-table-column>
                            <el-table-column prop="username" label="User Name" width="180"></el-table-column>
                            <el-table-column prop="nick" label="Nick" width="240"></el-table-column>
                            <el-table-column prop="school" label="School" width="160"></el-table-column>
                            <el-table-column fixed="right">
                                <template slot-scope="scope">
                                    <el-button type="danger" size="mini" plain icon="el-icon-delete" @click="userDelete(scope.row, scope.$index)"></el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-tab-pane>
                </el-tabs>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button plain @click="dialogFormVisible = false">Cancel</el-button>
                <el-button type="primary" plain @click="save(item)">Save</el-button>
            </div>
        </el-dialog>
        <select-problem></select-problem>
        <select-user></select-user>
    </div>
</template>

<script>
    import selectProblem from "../problem/select.vue";
    import selectUser from "../user/select.vue";

    export default {
        components: {
            selectProblem,
            selectUser
        },
        data(){
            return {
                dialogFormVisible: false,
                item: {},
                problems: [],
                users: [],
                private: false,
                title: 'Add Contest',
                start_time: '',
                end_time: '',
                activePane: 'problemPane',
            }
        },
        methods: {
            updateDescription(content) {
                this.item.description = content;
            },
            save(item) {
                let self = this;
                this.item.problem_list = this.problems.map(function(item) {
                    return item.id;
                });
                this.item.start_time = this.start_time;
                this.item.end_time = this.end_time;

                if(this.item.private) {
                    this.item.user_list = this.users.map(function(item) {
                        return user.id;
                    });
                }
                if (item.id) {
                    this.$http.put('admin/contests/' + item.id, item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-contests');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        });
                } else {
                    this.$http.post('admin/contests', item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-contests');
                        }).catch(function (resp) {
                            self.$message.error('Save failed!');
                        })
                }
            },
            changePrivate(event) {
                console.log(this.private);
                this.item.private = 0;
                if(this.private) {
                    this.item.private = 1;
                }
            },
            addProblem() {
                this.activePane = 'problemPane';
                this.$bus.emit('select-problem');
            },
            doAddProblem(item) {
                this.problems.push(item);
            },
            addUser() {
                this.activePane = 'userPane';
                this.$bus.emit('select-user');
            },
            doAddUser(item) {
                this.users.push(item);
            },
            userDelete(item, $index) {
                this.users.splice($index, 1);
            },
            handleDelete(item, $index) {
                this.problems.splice($index, 1);
            },
            handleUp(item, $index) {
                // remove item
                this.problems.splice($index, 1);
                // insert before it
                this.problems.splice($index - 1, 0, item);
            },
            handleDown(item, $index) {
                // remove item
                this.problems.splice($index, 1);
                // insert after it
                this.problems.splice($index + 1, 0, item);
            },
            loadProblems(item) {
                let self = this;
                this.$http.get('/admin/contests/' + item.id + '/problems')
                    .then(function (resp) {
                        self.problems = resp.data;
                    }).catch(function (resp) {
                        console.log(resp);
                    });
            },
            loadUsers(item) {
                let self = this;
                this.$http.get('/admin/contests/' + item.id + '/users')
                    .then(function (resp) {
                        self.users = resp.data;
                    }).catch(function (resp) {
                        console.log(resp);
                    });
            },
            initial() {
                this.private = false;
                this.users = [];
                this.problems = [];
            }
        },
        created() {
            let self = this;
            this.$bus.on('edit-contest', function(item) {
                if (item.id) {
                    self.item = JSON.parse(JSON.stringify(item));
                    self.title = 'Edit Contest';
                    self.loadProblems(self.item);
                    self.start_time = self.item.start_time;
                    self.end_time = self.item.end_time;
                    self.private = false;
                    if(item.private == 1) {
                        self.private = true;
                        self.loadUsers(self.item);
                    }
                } else {
                    self.item = {};
                    self.initial();
                }

                self.dialogFormVisible = true;
            });
            this.$bus.on('problem-selected', function(item) {
                self.doAddProblem(item);
            });
            this.$bus.on('user-selected', function(item) {
                self.doAddUser(item);
            })
        }
    }
</script>
