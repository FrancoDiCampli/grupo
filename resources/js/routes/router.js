import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home.vue";
import Welcome from "../views/Welcome.vue";
import NotFound from "../views/NotFound.vue";

// Sucursales
import Sucursales from "../views/sucursales/Sucursales";
import SucursalesCreate from "../views/sucursales/Create";
import SucursalesShow from "../views/sucursales/Show";

// Roles
import Roles from "../views/roles/Roles";
import RolesCreate from "../views/roles/Create";
import RolesEdit from "../views/roles/Edit";

// Users
import Users from "../views/users/Users";
import UsersCreate from "../views/users/Create";

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

        // Sucursales
        {
            path: "/sucursales",
            name: "sucursales",
            component: Sucursales
        },
        {
            path: "/sucursales/nueva",
            name: "sucursales_create",
            component: SucursalesCreate
        },
        {
            path: "/sucursales/show/:id",
            name: "sucursales_show",
            component: SucursalesShow,
            props: true
        },

        // Roles
        {
            path: "/roles",
            name: "roles",
            component: Roles
        },
        {
            path: "/roles/nuevo",
            name: "roles_create",
            component: RolesCreate
        },
        {
            path: "/roles/editar",
            name: "roles_edit",
            component: RolesEdit
        },

        // Users
        {
            path: "/users",
            name: "users",
            component: Users
        },
        {
            path: "/users/nuevo",
            name: "users_create",
            component: UsersCreate
        }
    ]
});
