import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home";
import Welcome from "../views/Welcome";
import NotFound from "../views/NotFound";
import AccessDenied from "../views/AccessDenied";

// Pedidos
import Pedidos from "../views/pedidos/Pedidos";
import PedidosCreate from "../views/pedidos/Create";
import PedidosEdit from "../views/pedidos/Edit";
import PedidosShow from "../views/pedidos/Show";

// Remitos
import Remitos from "../views/remitos/Remitos";
import RemitosShow from "../views/remitos/Show";

// Facturas
import Facturas from "../views/facturas/Facturas";
import FacturasCreate from "../views/facturas/Create";
import FacturasShow from "../views/facturas/Show";

// Entregas
import Entregas from "../views/entregas/Entregas";
import EntregasCreate from "../views/entregas/Create";
import EntregasShow from "../views/entregas/Show";

// Clientes
import Clientes from "../views/clientes/Clientes";
import ClientesCreate from "../views/clientes/Create";
import ClientesShow from "../views/clientes/Show";
import MiCuenta from "../views/clientes/MiCuenta";
import ShowRecibos from "../views/clientes/ShowRecibos";
import ResumenCuenta from "../views/clientes/ResumenCuenta";

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

// Devoluciones
import Devoluciones from "../views/devoluciones/Devoluciones";
import DevolucionesCreate from "../views/devoluciones/Create";

// Reportes
import Reportes from "../views/reportes/Reportes";

// Movimientos
import Movimientos from "../views/movimientos/Movimientos";

// Cartera
import Cartera from "../views/cartera/Cartera";

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

        // Pedidos
        {
            path: "/pedidos",
            name: "pedidos",
            component: Pedidos
        },
        {
            path: "/pedidos/nuevo",
            name: "pedidos_nuevo",
            component: PedidosCreate
        },
        {
            path: "/pedidos/editar/:id",
            name: "pedidos_editar",
            component: PedidosEdit,
            props: true
        },
        {
            path: "/pedidos/show/:id",
            name: "pedidos_show",
            component: PedidosShow,
            props: true
        },

        // Remitos
        {
            path: "/remitos",
            name: "remitos",
            component: Remitos
        },
        {
            path: "/remitos/show/:id",
            name: "remitos_show",
            component: RemitosShow,
            props: true
        },

        // Facturas
        {
            path: "/facturas",
            name: "facturas",
            component: Facturas
        },
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

        // Entregas
        {
            path: "/entregas",
            name: "entregas",
            component: Entregas
        },
        {
            path: "/entregas/create",
            name: "entregasCreate",
            component: EntregasCreate
        },
        {
            path: "/entregas/show/:id",
            name: "entregas_show",
            component: EntregasShow,
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
        {
            path: "/clientes/resumenCuenta",
            name: "clientes_resumenCuenta",
            component: ResumenCuenta
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

        // Devoluciones
        {
            path: "/devoluciones",
            name: "devoluciones",
            component: Devoluciones
        },
        {
            path: "/devoluciones/nueva",
            name: "devoluciones_nueva",
            component: DevolucionesCreate
        },

        // Reportes
        {
            path: "/reportes",
            name: "reportes",
            component: Reportes
        },

        // Movimientos
        {
            path: "/movimientos",
            name: "movimientos",
            component: Movimientos
        },

        // Cartera
        {
            path: "/cartera",
            name: "cartera",
            component: Cartera
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
