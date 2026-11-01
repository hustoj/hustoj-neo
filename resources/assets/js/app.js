/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import mitt from 'mitt';

import ElementPlus, { ElMessage } from 'element-plus';
import locale from 'element-plus/es/locale/lang/en';
import 'element-plus/dist/index.css';
import '../sass/font-awesome.min.css';

import axios from 'axios';

import App from './App.vue';
import HtmlEditor from './components/HtmlEditor.vue';
import routes from './routes';

axios.defaults.baseURL = '/';
axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response.status == 401) {
        return window.location.href = axios.defaults.baseURL;
    }
    ElMessage.error(error.response.statusText);
});

const router = createRouter({
    history: createWebHashHistory(),
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        return { left: 0, top: 0 };
    },
    routes: routes
});

const app = createApp(App);

app.use(router);
app.use(ElementPlus, { locale });
app.config.globalProperties.$bus = mitt();
app.config.globalProperties.$http = axios;
app.component('vue-html5-editor', HtmlEditor);

app.mount('#app');
