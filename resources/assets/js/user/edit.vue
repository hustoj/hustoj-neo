<template>
    <el-dialog width="80%" :title="title" :visible.sync="dialogFormVisible">
        <el-form ref="form" :rules="userRules" label-width="160px" :model="user">
            <el-form-item label="Account" required>
                <el-input v-model="user.username"></el-input>
            </el-form-item>
            <el-form-item label="Email">
                <el-input v-model="user.email"></el-input>
            </el-form-item>
            <el-form-item label="Nick">
                <el-input v-model="user.nick"></el-input>
            </el-form-item>
            <el-form-item label="Password">
                <el-input type="password" v-model="user.password"></el-input>
            </el-form-item>
            <el-form-item label="Confirm Password" prop="confirmPass">
                <el-input type="password" v-model="user.confirm_password"></el-input>
            </el-form-item>
            <el-form-item label="School">
                <el-input v-model="user.school"></el-input>
            </el-form-item>
            <el-form-item label="Locale">
                <el-input v-model="user.locale"></el-input>
            </el-form-item>
            <el-form-item label="User Status">
                <el-select v-model="user.status">
                    <el-option v-for="item in options" :key="item.value" :label="item.label"
                               :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="Roles">
                <el-transfer v-model="user.roles_edit"
                             :data="roles"
                             :titles="['Available Roles', 'Current Roles']"
                ></el-transfer>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button plain @click="dialogFormVisible = false">Cancel</el-button>
            <el-button type="primary" plain @click="save(user)">Save</el-button>
        </div>
    </el-dialog>
</template>

<script>

    export default {
        data() {
            let self = this;
            let confirmPassword = (rule, value, callback) => {
                if (self.user.password || self.user.confirm_password) {
                    if (self.user.password !== self.user.confirm_password) {
                        callback(new Error('Password is not equal'));
                    }
                } else {
                    callback();
                }
            };
            return {
                dialogFormVisible: false,
                user: {},
                options: [
                    {
                        value: 0,
                        label: 'Active',
                    }, {
                        value: 1,
                        label: 'Inactive',
                    }
                ],
                roles: [],
                userRules: {
                    confirmPass: [
                        {
                            validator: confirmPassword,
                            trigger: 'blur'
                        },
                    ]
                },
                title: 'Add User',
            }
        },
        methods: {
            save(item) {
                let self = this;
                if (item.id) {
                    this.$http.put('admin/users/' + item.id, item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-users');
                        }).catch(function (resp) {
                        self.$message.error('Save failed!');
                    });
                } else {
                    this.$http.post('admin/users', item)
                        .then(function (resp) {
                            self.$message.success('Save success!');
                            self.dialogFormVisible = false;
                            self.$bus.emit('reload-users');
                        }).catch(function (resp) {
                        self.$message.error('Save failed!');
                    })
                }
            },
            loadRoles() {
                let self = this;
                this.$http.get('/admin/roles').then(function (res) {
                    let data = [];
                    res.data.data.forEach((item) => {
                        data.push({
                            key: item.id,
                            label: item.name
                        });
                    });
                    self.roles = data;
                })
            }
        },
        created() {
            let self = this;
            this.$bus.on('edit-user', function (user) {
                self.user = JSON.parse(JSON.stringify(user));
                if (user.id) {
                    self.title = 'Edit User';
                }
                self.dialogFormVisible = true;
            });

            this.loadRoles();
        }
    }
</script>
