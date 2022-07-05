require('./bootstrap');

import Vue from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import PerfectScrollbar from 'vue2-perfect-scrollbar'
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'
import VCalendar from 'v-calendar'
import VueObserveVisibility from 'vue-observe-visibility'
import VueCompositionAPI from '@vue/composition-api'
import { Vuelidate } from 'vuelidate';
import store from './vuex/';
import generals from './vuex/store/generals';
export const bus = new Vue()

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);

AOS.init();

const options = {
    position: "bottom-right",
    timeout: 10000,
    closeOnClick: false,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 2,
    showCloseButtonOnHover: true,
    hideProgressBar: true,
    closeButton: "button",
    rtl: false,
    transition: "Vue-Toastification__fade",
    maxToasts: 24,
    newestOnTop: false
}

Vue.use(Toast, options)
Vue.use(PerfectScrollbar)
Vue.use(VCalendar, {
    componentPrefix: 'v',
});
Vue.use(VueObserveVisibility)
Vue.use(VueCompositionAPI)
Vue.use(Vuelidate)

const app = new Vue({
    el: '#app',
    store,
    mixins: [generals],
    mounted () {
        this.$store.dispatch('fetchPreferences')

        window.addEventListener('click', () => {
            this.$store.state.profileView.profileMoreOptions = false
        })
    }
});
