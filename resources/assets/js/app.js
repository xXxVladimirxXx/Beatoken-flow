require('./bootstrap');

import "./config"

import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import Notifications from 'vue-notification'
import modules from './store';
import beatokenRoutes from './beatoken-routes';
import money from 'v-money'

import App from './App'

Vue.use(Vuex)
Vue.use(VueRouter)
Vue.use(Notifications)

export const store = new Vuex.Store({
    ...modules
})

Vue.prototype.$loader = function (loader) {
    store.commit('general/setLoader', loader)
}

Vue.use(money, {
    decimal: '.',
    thousands: '',
    prefix: '',
    precision: 2,
})

// Authorization logic
import {initialize} from './generalRouterSetting';

initialize(store, beatokenRoutes.routes)

new Vue({
    el: '#beatoken-root',
    router: beatokenRoutes.routes,
    store,
    render: h => h(App)
})
