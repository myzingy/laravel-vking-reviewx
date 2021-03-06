
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import 'element-ui/lib/theme-default/index.css'
//Vue.component('example', require('./components/Example.vue'));
Vue.component('oneday-comment-list', require('./components/oneday-comment-list.vue'));
Vue.component('oneday-comment-form', require('./components/oneday-comment-form.vue'));
Vue.component('oneday-me-list', require('./components/oneday-me-list.vue'));

const app = new Vue({
    el: '#app'
});
