import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home";
import Welcome from "../views/Welcome";
import NotFound from "../views/NotFound";
import AccessDenied from "../views/AccessDenied";

// Articulos
import Articulos from "../views/articulos/Articulos";
import ArticulosCreate from "../views/articulos/Create";
import ArticulosShow from "../views/articulos/Show";

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
import UsersEdit from "../views/users/Edit";

Vue.use(Router);

export default new Router({
    mode: "history",
    routes: [
        {
            path: "*",
            component: NotFound
        },
        {
            path: "/accessd_denied",
            component: AccessDenied
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

        // Articulos
        {
            path: "/articulos",
            name: "articulos",
            component: Articulos
        },
        {
            path: "/articulos/nuevo",
            name: "articulos_nuevo",
            component: ArticulosCreate
        },
        {
            path: "/articulos/show/:id",
            name: "articulos_show",
            component: ArticulosShow,
            props: true
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
        },
        {
            path: "/users/editar",
            name: "users_edit",
            component: UsersEdit
        }
    ]
});
