import Vue from 'vue';


import home from "./pages/home.vue";
import problemIndex from "./problem/index.vue";
import userIndex from "./user/index.vue";
import roleIndex from "./role/index.vue";
import contestIndex from "./contest/index.vue";
import articleIndex from "./article/index.vue";
import optionIndex from "./options/index.vue";

import NotFoundComponent from "./pages/404.vue";


const routes = [
    {path: '*', component: NotFoundComponent},
    {path: '/', component: home, name: "home"},
    {path: '/problem', component: problemIndex, name: "problem.index"},
    {path: '/user', component: userIndex, name: "user.index"},
    {path: '/role', component: roleIndex, name: "role.index"},
    {path: '/contest', component: contestIndex, name: "contest.index"},
    {path: '/article', component: articleIndex, name: "article.index"},
    {path: '/settings', component: optionIndex, name: "option.index"}
];

export default routes;
