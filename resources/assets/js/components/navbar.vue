<template>
    <div class="navbar">
        <el-menu :default-active="activeIndex" class="el-menu" mode="horizontal" router
                 background-color="#545c64" text-color="#fff" active-text-color="#ffd04b">
            <el-menu-item index="#">Home</el-menu-item>
            <el-menu-item index="/">Dashboard</el-menu-item>
            <el-menu-item index="/article">Article</el-menu-item>
            <el-menu-item index="/problem">Problem</el-menu-item>
            <el-menu-item index="/contest">Contest</el-menu-item>
            <el-menu-item index="/user">User</el-menu-item>
            <el-submenu index="2">
                <template slot="title">Manage</template>
                <el-menu-item index="/role">Role</el-menu-item>
                <el-menu-item index="/settings">Setting</el-menu-item>
            </el-submenu>
        </el-menu>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                activeIndex: '/',
            }
        }
    }
</script>
