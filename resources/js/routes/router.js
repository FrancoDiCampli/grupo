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

// Facturas
import FacturasCreate from "../views/facturas/Create";
import FacturasShow from "../views/facturas/Show";

// Presupuestos
import Presupuestos from "../views/presupuestos/presupuestos";
import PresupuestosCreate from "../views/presupuestos/Create";
import PresupuestosShow from "../views/presupuestos/Show";

// Clientes
import Clientes from "../views/clientes/Clientes";
import ClientesCreate from "../views/clientes/Create";
import ClientesShow from "../views/clientes/Show";
import MiCuenta from "../views/clientes/MiCuenta";
import ShowRecibos from "../views/clientes/ShowRecibos";

// Articulos
import Articulos from "../views/articulos/Articulos";
import ArticulosCreate from "../views/articulos/Create";
import ArticulosShow from "../views/articulos/Show";

// Proveedores
import Proveedores from "../views/proveedores/Proveedores";
import ProveedoresCreate from "../views/proveedores/Create";
import ProveedoresShow from "../views/proveedores/Show";

// Compras
import Compras from "../views/compras/Compras";
import ComprasCreate from "../views/compras/Create";
import ComprasShow from "../views/compras/Show";

// Consignaciones
import Consignaciones from "../views/consignaciones/Consignaciones";
import ConsignacionesCreate from "../views/consignaciones/Create";
import ConsignacionesShow from "../views/consignaciones/Show";

// Movimientos
import Movimientos from "../views/movimientos/Movimientos";

// Roles
import Roles from "../views/roles/Roles";
import RolesCreate from "../views/roles/Create";
import RolesEdit from "../views/roles/Edit";

// Users
import Users from "../views/users/Users";
import UsersCreate from "../views/users/Create";
import UsersEdit from "../views/users/Edit";

// Reportes
import Cartera from "../views/reportes/Cartera";
import Reportes from "../views/reportes/Reportes";

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
            component: VentasShow,
            props: true
        },

        // Facturas
        {
            path: "/facturas/create",
            name: "facturasCreate",
            component: FacturasCreate
        },
        {
            path: "/facturas/show/:id",
            name: "facturas_show",
            component: FacturasShow,
            props: true
        },

        // Presupuestos
        {
            path: "/presupuestos",
            name: "presupuestos",
            component: Presupuestos
        },
        {
            path: "/presupuestos/nuevo",
            name: "presupuestos_nuevo",
            component: PresupuestosCreate
        },
        {
            path: "/presupuestos/show/:id",
            name: "presupuestos_show",
            component: PresupuestosShow,
            props: true
        },

        // Clientes
        {
            path: "/clientes",
            name: "clientes",
            component: Clientes
        },
        {
            path: "/clientes/nuevo",
            name: "clientes_nuevo",
            component: ClientesCreate
        },
        {
            path: "/clientes/show/:id",
            name: "clientes_show",
            component: ClientesShow,
            props: true
        },
        {
            path: "/clientes/micuenta",
            name: "mi_cuenta",
            component: MiCuenta
        },
        {
            path: "/clientes/showRecibos/:id",
            name: "clientes_showRecibos",
            component: ShowRecibos,
            props: true
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

        // Compras
        {
            path: "/compras",
            name: "compras",
            component: Compras
        },
        {
            path: "/compras/nueva",
            name: "compras_nueva",
            component: ComprasCreate
        },
        {
            path: "/compras/show/:id",
            name: "compras_show",
            component: ComprasShow,
            props: true
        },

        // Consignaciones
        {
            path: "/consignaciones",
            name: "consignaciones",
            component: Consignaciones
        },
        {
            path: "/consignaciones/nueva",
            name: "consignaciones_nueva",
            component: ConsignacionesCreate
        },
        {
            path: "/consignaciones/show/:id",
            name: "consignaciones_show",
            component: ConsignacionesShow,
            props: true
        },

        // Movimientos
        {
            path: "/movimientos",
            name: "movimientos",
            component: Movimientos
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
        },

        // Reportes
        // {
        //     path: "/cartera",
        //     name: "cartera",
        //     component: Cartera
        // },
        {
            path: "/reportes",
            name: "reportes",
            component: Reportes
        }
    ]
});
