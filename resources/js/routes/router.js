import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home";
import Welcome from "../views/Welcome";
import NotFound from "../views/NotFound";
import AccessDenied from "../views/AccessDenied";

// Ventas
import Ventas from "../views/ventas/Ventas";
import VentasCreate from "../views/ventas/Create";
import VentasShow from "../views/ventas/Show";

// Articulos
import Articulos from "../views/articulos/Articulos";
import ArticulosCreate from "../views/articulos/Create";
import ArticulosShow from "../views/articulos/Show";

// Proveedores
import Proveedores from "../views/proveedores/Proveedores";
import ProveedoresCreate from "../views/proveedores/Create";
import ProveedoresShow from "../views/proveedores/Show";

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

        // Ventas
        {
            path: "/ventas",
            name: "ventas",
            component: Ventas
        },
        {
            path: "/ventas/nueva",
            name: "ventas_nueva",
            component: VentasCreate
        },
        {
            path: "/ventas/show/:id",
            name: "ventas_show",
            component: VentasShow
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

        // Proveedores
        {
            path: "/proveedores",
            name: "proveedores",
            component: Proveedores
        },
        {
            path: "/proveedores/nuevo",
            name: "proveedores_nuevo",
            component: ProveedoresCreate
        },
        {
            path: "/proveedores/show/:id",
            name: "proveedores_show",
            component: ProveedoresShow,
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
