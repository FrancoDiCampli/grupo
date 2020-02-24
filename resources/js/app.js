import Vue from "vue";
import App from "./App.vue";
import router from "./routes/router";
import store from "./store/store";

// Style
import "../sass/app.scss";

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
