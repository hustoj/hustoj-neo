/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import VueRouter from "vue-router";
Vue.use(VueRouter);

import VueBus from 'vue-bus';
Vue.use(VueBus);

import ElementUI from "element-ui";
import locale from 'element-ui/lib/locale/lang/en'
import "element-ui/lib/theme-chalk/index.css";
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
