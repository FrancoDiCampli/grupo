<template>
    <div v-if="$store.state.auth.user">
        <!-- Barra de navegacion lateral -->
        <v-navigation-drawer
            v-model="drawer"
            :mini-variant="mini"
            :permanent="permanent"
            :temporary="temporary"
            stateless
            app
            hide-overlay
        >
            <v-list-item class="drawer-action primary" dark>
                <v-list-item-icon @click="mini = !mini" class="drawer-action-icon hidden-xs-only">
                    <v-icon v-if="mini">fas fa-bars</v-icon>
                    <v-icon v-else style="margin-left: 6px;">fas fa-times</v-icon>
                </v-list-item-icon>
                <v-list-item-icon class="hidden-sm-and-up" style="margin-left: 6px;">
                    <v-icon>fas fa-times</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list dense class="drawer-routes">
                <div v-for="(route, index) in routes" :key="index">
                    <v-list-item
                        @click="$vuetify.breakpoint.xsOnly ? drawer = false : drawer = true"
                        :to="route.url"
                        v-if="route.roles.find(element => {return element == $store.state.auth.user.rol})"
                        color="primary"
                        link
                    >
                        <v-list-item-icon>
                            <v-icon>{{ route.icon }}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>{{ route.name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider v-if="route.divider"></v-divider>
                </div>
            </v-list>
        </v-navigation-drawer>

        <!-- Navbar superior -->
        <v-app-bar color="primary" dark flat app fixed>
            <!-- Boton para activar el navigation drawer -->
            <v-app-bar-nav-icon
                @click="drawer = true"
                class="hidden-sm-and-up"
                v-if="$store.state.auth.user.user != null"
            ></v-app-bar-nav-icon>

            <!-- Titulo de la aplicacion -->
            <v-toolbar-title style="cursor: pointer;">Grupo APC</v-toolbar-title>
            <v-spacer></v-spacer>

            <!-- BARRA DE BUSQUEDA DE GERMAN -->
            <!-- <v-row v-if="$store.state.auth.user.rol.role != 'cliente'">
                <v-combobox
                    label="Buscar"
                    @keyup="search()"
                    v-model="buscar"
                    persistent-hint
                    :hide-no-data="noMostrar"
                >
                    <template v-slot:no-data>
                        <div v-if="clientes.length > 0">
                            <v-subheader>CLIENTES</v-subheader>
                            <v-list-item v-for="(cliente, i) in clientes" :key="i">
                                <v-list-item-content>
                                    <v-list-item
                                        @click="$router.push('/clientes/show/' + cliente.id); buscar = null; clientes = []; articulos = []; negocios = []; proveedores = [];noMostrar = true;"
                                    >{{cliente.razonsocial}}</v-list-item>
                                </v-list-item-content>
                            </v-list-item>
                            <v-divider></v-divider>
                        </div>
                        <div v-if="articulos.length > 0">
                            <v-subheader>ARTICULOS</v-subheader>
                            <v-list-item v-for="(article, i) in articulos" :key="i">
                                <v-list-item-content>
                                    <v-list-item
                                        @click="$router.push('/articulos/show/' + article.id); buscar = null; clientes = []; articulos = [];negocios = []; proveedores = []; noMostrar = true;"
                                    >{{article.articulo}}</v-list-item>
                                </v-list-item-content>
                            </v-list-item>
                            <v-divider></v-divider>
                        </div>
                        <div v-if="negocios.length > 0">
                            <v-subheader>NEGOCIOS</v-subheader>
                            <v-list-item v-for="(negocio, i) in negocios" :key="i">
                                <v-list-item-content>
                                    <v-list-item
                                        @click="$router.push('/negocios/show/' + negocio.id); buscar = null; clientes = []; articulos = []; negocios = []; proveedores = [];noMostrar = true;"
                                    >{{negocio.razonsocial}}</v-list-item>
                                </v-list-item-content>
                            </v-list-item>
                        </div>
                        <div v-if="proveedores.length > 0">
                            <v-subheader>PROVEEDORES</v-subheader>
                            <v-list-item v-for="(proveedor, i) in proveedores" :key="i">
                                <v-list-item-content>
                                    <v-list-item
                                        @click="$router.push('/proveedores/show/' + proveedor.id); buscar = null; clientes = []; articulos = []; negocios = []; proveedores = [];noMostrar = true;"
                                    >{{proveedor.razonsocial}}</v-list-item>
                                </v-list-item-content>
                            </v-list-item>
                        </div>
                    </template>
                </v-combobox>
            </v-row>
            <v-spacer></v-spacer>-->

            <!-- Menu del usuario -->
            <v-menu offset-y v-if="$store.state.auth.user.user != null">
                <template v-slot:activator="{ on }">
                    <v-avatar color="secondary" style="cursor: pointer;" v-on="on">
                        <img
                            v-if="$store.state.auth.user.user.foto != null"
                            :src="$store.state.auth.user.user.foto"
                            width="100%"
                            height="auto"
                        />
                        <span
                            v-else-if="$store.state.auth.user.user.name"
                            class="text-uppercase"
                        >{{ $store.state.auth.user.user.name[0] }}</span>
                    </v-avatar>
                </template>
                <v-list>
                    <v-list-item @click="sidenav = true">
                        <v-list-item-title>Perfil</v-list-item-title>
                    </v-list-item>
                    <slot></slot>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Sidenav de usuario -->
        <v-slide-x-reverse-transition>
            <v-col cols="12" sm="5" lg="4" v-show="sidenav" class="sidenav pa-0">
                <v-card tile class="sidenav-overflow">
                    <v-toolbar color="secondary" dark flat>
                        <v-toolbar-title>Perfil</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn @click="sidenav = false" icon>
                            <v-icon>fas fa-arrow-right</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <Account v-if="$store.state.auth.user.user != null"></Account>
                </v-card>
            </v-col>
        </v-slide-x-reverse-transition>
    </div>
</template>

<script>
import Account from "./auth/Account";

export default {
    data: () => ({
        buscar: null,
        clientes: [],
        proveedores: [],
        articulos: [],
        negocios: [],
        noMostrar: true,
        drawer: false,
        mini: false,
        permanent: false,
        temporary: true,
        sidenav: false,
        routes: [
            {
                name: "Ventas",
                icon: "fas fa-dollar-sign",
                url: "/ventas",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: false
            },
            {
                name: "Presupuestos",
                icon: "fas fa-file",
                url: "/presupuestos",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: false
            },
            {
                name: "Cartera",
                icon: "fas fa-wallet",
                url: "/cartera",
                roles: ["superAdmin", "administrador"],
                divider: true
            },
            {
                name: "Clientes",
                icon: "fas fa-user-friends",
                url: "/clientes",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: false
            },

            {
                name: "Articulos",
                icon: "fas fa-box-open",
                url: "/articulos",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: true
            },
            {
                name: "Proveedores",
                icon: "fas fa-truck-moving",
                url: "/proveedores",
                roles: ["superAdmin", "administrador"],
                divider: false
            },
            {
                name: "Compras",
                icon: "fas fa-shopping-cart",
                url: "/compras",
                roles: ["superAdmin", "administrador"],
                divider: true
            },
            {
                name: "Negocios",
                icon: "fas fa-building",
                url: "/negocios",
                roles: ["superAdmin", "administrador"],
                divider: true
            },
            {
                name: "Reportes",
                icon: "fas fa-clipboard",
                url: "/reportes",
                roles: ["superAdmin", "administrador"],
                divider: false
            },
            {
                name: "Movimientos",
                icon: "fas fa-chart-line",
                url: "/movimientos",
                roles: ["superAdmin", "administrador"],
                divider: true
            },
            {
                name: "Usuarios",
                icon: "fas fa-users",
                url: "/users",
                roles: ["superAdmin", "administrador"],
                divider: false
            },
            {
                name: "Roles",
                icon: "fas fa-tag",
                url: "/roles",
                roles: ["superAdmin"],
                divider: false
            }
        ]
    }),

    components: {
        Account
    },

    computed: {
        isMobile() {
            return this.$vuetify.breakpoint.xsOnly;
        }
    },

    watch: {
        isMobile() {
            this.drawerControl();
        }
    },

    mounted() {
        this.drawerControl();
    },

    methods: {
        drawerControl() {
            if (this.isMobile) {
                this.drawer = false;
                this.mini = false;
                this.permanet = false;
                this.temporary = true;
            } else {
                this.drawer = true;
                this.mini = true;
                this.permanet = true;
                this.temporary = false;
            }
        },

        search: async function() {
            if (this.buscar != "" && this.buscar != null) {
                axios
                    .post("api/buscando", { buscar: this.buscar })
                    .then(response => {
                        this.clientes = response.data.clientes;
                        this.proveedores = response.data.proveedores;
                        this.articulos = response.data.articulos;
                        this.negocios = response.data.negocios;
                        this.noMostrar = false;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            } else {
                this.clientes = [];
                this.negocios = [];
                this.articulos = [];
                this.proveedores = [];
                this.noMostrar = true;
            }
        }
    }
};
</script>

<style>
.sidenav {
    position: fixed;
    right: 0;
    width: 100%;
    z-index: 9;
    height: 100vh;
}

.sidenav-overflow {
    height: 100vh;
    max-height: 100vh;
    overflow: auto;
}

.sidenav-overflow::-webkit-scrollbar {
    width: 6px;
}

.sidenav-overflow::-webkit-scrollbar-thumb {
    background-color: rgba(189, 189, 189, 0.75);
}

.drawer-action {
    height: 63.5px;
}

.drawer-action-icon {
    cursor: pointer;
    margin-top: 20px !important;
}

@media (max-width: 960px) {
    .drawer-action {
        height: 55.5px;
    }

    .drawer-action-icon {
        margin-top: 16px !important;
    }
}

.drawer-routes {
    margin-top: -8px;
}
</style>