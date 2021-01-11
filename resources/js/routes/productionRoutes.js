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
import DevolucionesShow from "../views/devoluciones/Show";

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

const originalPush = Router.prototype.push;
Router.prototype.push = function push(location) {
  return originalPush.call(this, location).catch(err => {
    if (err.name !== 'NavigationDuplicated') throw err
  });
}

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
            name: "accessd_denied",
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
            component: Pedidos,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/pedidos/nuevo",
            name: "pedidos_nuevo",
            component: PedidosCreate,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/pedidos/editar/:id",
            name: "pedidos_editar",
            component: PedidosEdit,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/pedidos/show/:id",
            name: "pedidos_show",
            component: PedidosShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },

        // Remitos
        {
            path: "/remitos",
            name: "remitos",
            component: Remitos,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/remitos/show/:id",
            name: "remitos_show",
            component: RemitosShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },

        // Facturas
        {
            path: "/facturas",
            name: "facturas",
            component: Facturas,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/facturas/create",
            name: "facturasCreate",
            component: FacturasCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/facturas/show/:id",
            name: "facturas_show",
            component: FacturasShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Entregas
        {
            path: "/entregas",
            name: "entregas",
            component: Entregas,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/entregas/create",
            name: "entregasCreate",
            component: EntregasCreate,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/entregas/show/:id",
            name: "entregas_show",
            component: EntregasShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },

        // Clientes
        {
            path: "/clientes",
            name: "clientes",
            component: Clientes,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/clientes/nuevo",
            name: "clientes_nuevo",
            component: ClientesCreate,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/clientes/show/:id",
            name: "clientes_show",
            component: ClientesShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/clientes/micuenta",
            name: "mi_cuenta",
            component: MiCuenta,
            meta: {
                rol: ["cliente"],
                redirect: "welcome"
            }
        },
        {
            path: "/clientes/showRecibos/:id",
            name: "clientes_showRecibos",
            component: ShowRecibos,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor', 'cliente'],
                redirect: "welcome"
            }
        },

        // Articulos
        {
            path: "/articulos",
            name: "articulos",
            component: Articulos,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },
        {
            path: "/articulos/nuevo",
            name: "articulos_nuevo",
            component: ArticulosCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/articulos/show/:id",
            name: "articulos_show",
            component: ArticulosShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador", 'vendedor'],
                redirect: "welcome"
            }
        },

        // Proveedores
        {
            path: "/proveedores",
            name: "proveedores",
            component: Proveedores,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/proveedores/nuevo",
            name: "proveedores_nuevo",
            component: ProveedoresCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/proveedores/show/:id",
            name: "proveedores_show",
            component: ProveedoresShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Compras
        {
            path: "/compras",
            name: "compras",
            component: Compras,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/compras/nueva",
            name: "compras_nueva",
            component: ComprasCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/compras/show/:id",
            name: "compras_show",
            component: ComprasShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Consignaciones
        {
            path: "/consignaciones",
            name: "consignaciones",
            component: Consignaciones,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/consignaciones/nueva",
            name: "consignaciones_nueva",
            component: ConsignacionesCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/consignaciones/show/:id",
            name: "consignaciones_show",
            component: ConsignacionesShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Devoluciones
        {
            path: "/devoluciones",
            name: "devoluciones",
            component: Devoluciones,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/devoluciones/nueva",
            name: "devoluciones_nueva",
            component: DevolucionesCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/devoluciones/show/:id",
            name: "devoluciones_show",
            component: DevolucionesShow,
            props: true,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Reportes
        {
            path: "/reportes",
            name: "reportes",
            component: Reportes,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Movimientos
        {
            path: "/movimientos",
            name: "movimientos",
            component: Movimientos,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Cartera
        {
            path: "/cartera",
            name: "cartera",
            component: Cartera,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },

        // Roles
        {
            path: "/roles",
            name: "roles",
            component: Roles,
            meta: {
                rol: ["superAdmin"],
                redirect: "welcome"
            }
        },
        {
            path: "/roles/nuevo",
            name: "roles_create",
            component: RolesCreate,
            meta: {
                rol: ["superAdmin"],
                redirect: "welcome"
            }
        },
        {
            path: "/roles/editar",
            name: "roles_edit",
            component: RolesEdit,
            meta: {
                rol: ["superAdmin"],
                redirect: "welcome"
            }
        },

        // Users
        {
            path: "/users",
            name: "users",
            component: Users,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/users/nuevo",
            name: "users_create",
            component: UsersCreate,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        },
        {
            path: "/users/editar",
            name: "users_edit",
            component: UsersEdit,
            meta: {
                rol: ["superAdmin", "administrador"],
                redirect: "welcome"
            }
        }
    ]
});
