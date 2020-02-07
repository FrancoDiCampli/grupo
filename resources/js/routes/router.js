import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home.vue";
import Welcome from "../views/Welcome.vue";
import NotFound from "../views/NotFound.vue";

Vue.use(Router);

export default new Router({
    mode: "history",
    routes: [
        {
            path: "*",
            component: NotFound
        },
        {
            path: "/",
            name: "home",
            component: Home,
            meta: {
                rol: ["not_authorized"],
                redirect: "welcome"
            }
        },
        {
            path: "/welcome",
            name: "welcome",
            component: Welcome,
            meta: {
                permission: "authenticated",
                redirect: "home"
            }
        }
    ]
});
