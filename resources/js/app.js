import Vue from "vue";
import App from "./App.vue";
import router from "./routes/router";
import store from "./store/store";

// Axios
window.axios = require("axios");

// Vuetify
import vuetify from "./plugins/vuetify";

// Vue Croppa
import "vue-croppa/dist/vue-croppa.css";
import Croppa from "vue-croppa";
Vue.use(Croppa);

// Vue Auth
import VueAuth from "./plugins/vue-auth";
Vue.use(VueAuth, { router });

Vue.prototype.$user.set({
    rol: "not_authorized",
    permissions: []
});

Vue.config.productionTip = false;

new Vue({
    router,
    store,
    vuetify,
    render: function(h) {
        return h(App);
    }
}).$mount("#app");
