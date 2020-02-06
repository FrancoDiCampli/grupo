import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home.vue";
import Welcome from "../views/Welcome.vue";
import NotFound from "../views/NotFound.vue";

import Register from "../views/auth/Register.vue";
import Login from "../views/auth/Login.vue";

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
        },

        //Auth Routes
        {
            path: "/register",
            name: "register",
            component: Register,
            meta: {
                rol: ["not_authorized"],
                redirect: "welcome"
            }
        },
        {
            path: "/login",
            name: "login",
            component: Login,
            meta: {
                rol: ["not_authorized"],
                redirect: "welcome"
            }
        }
    ]
});
