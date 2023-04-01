// import 'bootstrap'
import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);

/*
import Vue from 'vue';
import VueCookies from 'vue-cookies';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueAlertify from 'vue-alertify';
import axios from 'axios';
import App from './App';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

window.Vue = require('vue');

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueAlertify);
Vue.use(VueCookies);

Vue.prototype.$axios = axios;

new Vue({
    render: h => h(App),
}).$mount('#app')
*/