import Vue from 'vue';
import axios from 'axios';

// 이거 쓰면 인증이 안됨
// const instance = axios.create({
//     baseURL: process.env.apiUrl,
// });

Vue.prototype.$axios = axios;
