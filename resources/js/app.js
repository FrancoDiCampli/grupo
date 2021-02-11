import Vue from "vue";
import App from "./App.vue";
// Local routes
// import router from "./routes/router";
// Production routes
import router from "./routes/productionRoutes";
import store from "./store/store";
import dayjs from 'dayjs';

// Axios
window.axios = require("axios");

// Vuetify
import vuetify from "./plugins/vuetify";

// Vue Croppa
import "vue-croppa/dist/vue-croppa.css";
import Croppa from "vue-croppa";
Vue.use(Croppa);

// V-Charts
import VCharts from "v-charts";
Vue.use(VCharts);

// Vue Auth
import VueAuth from "./plugins/vue-auth";
Vue.use(VueAuth, { router });

Vue.prototype.$user.set({
    rol: "not_authorized",
    permissions: []
});

// DATE FILTER
Vue.filter("formatDate", function(value) {
    if (value) {
        return dayjs(String(value)).format("DD-MM-YYYY");
    }
});

// NUMBER FORMATS
Vue.filter("formatCurrency", function(value, currency) {
    if (value) {
        return Intl.NumberFormat('es-AR', {style: "currency", currency: currency}).format(Number(value));
    }
});

Vue.filter("formatNumber", function(value) {
    if (value) {
        return Intl.NumberFormat('es-AR').format(Number(value));
    }
});

// SCROOL DIRECTIVE
Vue.directive('scroll', {
    inserted: function (el, binding) {
        let f = function (evt) {
            if (binding.value(evt, el)) {
                window.removeEventListener('scroll', f)
            }
        }
        window.addEventListener('scroll', f)
    }
})

// ELIMINAR ADVERTENCIA AL USAR V-CALENDAR
const ignoreWarnMessage =
    "The .native modifier for v-on is only valid on components but it was used on <div>.";
Vue.config.warnHandler = function(msg, vm, trace) {
    if (msg === ignoreWarnMessage) {
        msg = null;
        vm = null;
        trace = null;
    }
};

Vue.config.productionTip = false;

new Vue({
    router,
    store,
    vuetify,
    render: function(h) {
        return h(App);
    }
}).$mount("#app");
