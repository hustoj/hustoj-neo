/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

import Vue from "vue";

import VueRouter from "vue-router";
Vue.use(VueRouter);

import VueBus from 'vue-bus';
Vue.use(VueBus);

import ElementUI from "element-ui";
import locale from 'element-ui/lib/locale/lang/en'
import "element-ui/lib/theme-default/index.css";
Vue.use(ElementUI, { locale });

import axios from "axios";

import VueCookie from "vue-cookie";
Vue.use(VueCookie);

import VueHtml5Editor from 'vue-html5-editor'
let options = {};
Vue.use(VueHtml5Editor, options);

import navbar from "./components/navbar";
Vue.component("navbar", navbar);
import ojfooter from './components/footer';
Vue.component("ojfooter", ojfooter);


Vue.prototype.$http = axios;
const BASE_URL = process.env.MIX_ADMIN_URL;
axios.defaults.baseURL = BASE_URL;
axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response.status == 401) {
        return window.location.href = BASE_URL;
    }
    Vue.$message.error(response.statusText);
});

import routes from './routes';


const router = new VueRouter({
    // mode: 'history',
    hashbang: true,
    saveScrollPosition: true,
    transitionOnLoad: true,
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return {x: 0, y: 0}
        }
    },
    routes: routes
});

const app = new Vue({
    router: router
}).$mount("#app");
