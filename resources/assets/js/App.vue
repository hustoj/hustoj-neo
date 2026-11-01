<template>
    <div class="container">
        <navbar></navbar>
        <div class="page-container el-main">
            <router-view></router-view>
        </div>
        <ojfooter></ojfooter>
    </div>
</template>

<script>
    import navbar from './components/navbar.vue';
    import ojfooter from './components/footer.vue';

    export default {
        components: {
            navbar,
            ojfooter,
        },
    }
</script>
