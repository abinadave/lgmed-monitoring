
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import VueRouter from 'vue-router'
import VueResource from 'vue-resource'
require('./bootstrap');
Vue.use(VueRouter);
Vue.use(VueResource);




/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#csrf-token').getAttribute('content');

$.ajaxSetup(
{
    headers:
    {
        'X-CSRF-Token': $('#csrf-token').val()
    }
});

window.router = new VueRouter({
  routes: [
    {
      path: '/LGMED/programs',
      name: 'programs',
      component: require('./components/programs/programs.vue')
    },
    {
      path: '/LGMED/users',
      name: 'users',
      component: require('./components/users/users.vue')
    },
    {
      path: '/LGMED/programs/:id',
      name: 'program-reports',
      component: require('./components/programs/program_view.vue')
    },
    { path: '/', redirect: '/LGMED/programs' }
  ]
});

const app = new Vue({
  	router
}).$mount('#app');


