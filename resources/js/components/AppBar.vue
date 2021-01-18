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
                <v-list-item-content v-if="!mini">
                    <v-list-item-title>
                        <b style="margin-top: 120px; !important">Grupo APC</b>
                    </v-list-item-title>
                </v-list-item-content>
                <v-list-item-icon
                    class="hidden-sm-and-up"
                    style="margin-left: 6px;"
                    @click="drawer = false"
                >
                    <v-icon>fas fa-times</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list dense class="drawer-routes">
                <div v-for="(route, index) in routes" :key="index">
                    <v-list-item
                        @click="
                            $vuetify.breakpoint.xsOnly
                                ? (drawer = false)
                                : (drawer = true)
                        "
                        :to="route.url"
                        v-if="
                            route.roles.find(element => {
                                return element == $store.state.auth.user.rol;
                            })
                        "
                        color="primary"
                        link
                    >
                        <v-list-item-icon>
                            <v-icon>{{ route.icon }}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{
                                route.name
                                }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider 
                        v-if="
                            route.divider && 
                            route.roles.find(element => {
                                return element == $store.state.auth.user.rol;
                            })
                        "
                    ></v-divider>
                </div>
                <v-list-item
                    @click="
                        $vuetify.breakpoint.xsOnly
                            ? (drawer = false)
                            : (drawer = true)
                    "
                    v-if="$store.state.auth.user.rol == 'cliente'"
                    href="http://www.grupoapc.com.ar/"
                    target="_blank"
                    color="primary"
                    link
                >
                    <v-list-item-icon>
                        <v-icon>fas fa-globe</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            grupoapc.com
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    @click="
                        $vuetify.breakpoint.xsOnly
                            ? (drawer = false)
                            : (drawer = true)
                    "
                    v-if="$store.state.auth.user.rol == 'cliente'"
                    href="https://play.google.com/store/apps/details?id=com.grupoapcapp.ar&hl=es"
                    target="_blank"
                    color="primary"
                    link
                >
                    <v-list-item-icon>
                        <v-icon>fas fa-mobile-alt</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            Grupo APC App
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
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

            <div v-if="$store.state.auth.user.rol != 'cliente'">
                <slot name="searchBar"></slot>
            </div>

            <v-spacer></v-spacer>
            <!-- Notificaciones -->
            <v-btn 
                icon 
                @click="openSide('noti')" 
                class="mx-2" 
                v-if="
                    $store.state.auth.user.rol == 'superAdmin' || 
                    $store.state.auth.user.rol == 'administrador'
                "
            >
                <v-badge
                    :content="$store.state.notificaciones.unread.length"
                    :value="$store.state.notificaciones.unread.length"
                    color="red"
                >
                    <v-icon>fas fa-bell</v-icon>
                </v-badge>
            </v-btn>

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
                    <v-list-item @click="openSide('user')">
                        <v-list-item-title>Perfil</v-list-item-title>
                    </v-list-item>
                    <slot name="userActions"></slot>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Sidenav usuarios/notificaciones -->
        <v-slide-x-reverse-transition>
            <v-col cols="12" sm="5" lg="4" v-show="sidenav.active" class="sidenav pa-0">
                <v-card tile class="sidenav-overflow">
                    <v-toolbar color="secondary" dark flat>
                        <v-toolbar-title v-if="sidenav.mode == 'user'">Perfil</v-toolbar-title>
                        <v-toolbar-title v-else-if="sidenav.mode == 'noti'">Notificaciones</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn @click="closeSide()" icon>
                            <v-icon>fas fa-arrow-right</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <div v-if="sidenav.mode == 'user'">
                        <Account v-if="$store.state.auth.user.user != null"></Account>
                    </div>
                    <div v-else-if="sidenav.mode == 'noti'">
                        <div v-if="$store.state.notificaciones.unread.length > 0">
                            <v-list dense>
                                <v-subheader>No leidas</v-subheader>
                                <v-list-item
                                    v-for="noti in $store.state.notificaciones.unread"
                                    :key="noti.id"
                                    @click="markRead(noti)"
                                >
                                    <v-list-item-icon>
                                        <v-icon color="amber">fas fa-box-open</v-icon>
                                    </v-list-item-icon>

                                    <v-list-item-content>
                                        <v-list-item-title>{{ noti.data.message }}</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </div>
                        <v-divider></v-divider>
                        <div v-if="$store.state.notificaciones.read.length > 0">
                            <v-list dense disabled>
                                <v-subheader>Leidas</v-subheader>
                                <v-list-item v-for="noti in $store.state.notificaciones.read" :key="noti.id">
                                    <v-list-item-icon>
                                        <v-icon>fas fa-box-open</v-icon>
                                    </v-list-item-icon>

                                    <v-list-item-content>
                                        <v-list-item-title>{{ noti.data.message }}</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </div>
                        <div v-if="
                            $store.state.notificaciones.unread.length <= 0 &&
                            $store.state.notificaciones.read.length <= 0    
                        ">
                            <h2 class="text-center py-2">No hay notificaciones</h2>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-slide-x-reverse-transition>
    </div>
</template>

<script>
import Account from "./auth/Account";

export default {
    data: () => ({
        clientes: [],
        proveedores: [],
        articulos: [],
        negocios: [],
        drawer: false,
        mini: false,
        permanent: false,
        temporary: true,
        sidenav: {
            active: false,
            mode: ''
        },
        routes: [
            {
                name: "Mi cuenta",
                icon: "fas fa-dollar-sign",
                url: "/clientes/micuenta",
                roles: ["cliente"],
                divider: false,
            },
            {
                name: "Pedidos",
                icon: "fas fa-receipt",
                url: "/pedidos",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: false,
            },
            {
                name: "Remitos",
                icon: "fas fa-file-alt",
                url: "/remitos",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: false,
            },
            {
                name: "Facturas",
                icon: "fas fa-file-invoice-dollar",
                url: "/facturas",
                roles: ["superAdmin", "administrador"],
                divider: false,
            },
            {
                name: "Entregas",
                icon: "fas fa-luggage-cart",
                url: "/entregas",
                roles: ["superAdmin", "administrador", "vendedor",],
                divider: true,
            },

            {
                name: "Clientes",
                icon: "fas fa-user-friends",
                url: "/clientes",
                roles: [
                    "superAdmin",
                    "administrador",
                    "vendedor",
                ],
                divider: false,
            },

            {
                name: "Articulos",
                icon: "fas fa-box-open",
                url: "/articulos",
                roles: [
                    "superAdmin",
                    "administrador",
                    "vendedor",
                ],
                divider: true,
            },
            {
                name: "Proveedores",
                icon: "fas fa-truck-moving",
                url: "/proveedores",
                roles: ["superAdmin", "administrador"],
                divider: false,
            },
            {
                name: "Compras",
                icon: "fas fa-shopping-cart",
                url: "/compras",
                roles: ["superAdmin", "administrador"],
                divider: false,
            },
            {
                name: "Consignaciones",
                icon: "fas fa-file-signature",
                url: "/consignaciones",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: false,
            },
            {
                name: "Devoluciones",
                icon: "fas fa-exchange-alt",
                url: "/devoluciones",
                roles: ["superAdmin", "administrador", "vendedor"],
                divider: true,
            },
            {
                name: "Reportes",
                icon: "fas fa-clipboard",
                url: "/reportes",
                roles: ["superAdmin", "administrador"],
                divider: false,
            },
            {
                name: "Movimientos",
                icon: "fas fa-chart-line",
                url: "/movimientos",
                roles: ["superAdmin", "administrador"],
                divider: false,
            },
            {
                name: "Cartera",
                icon: "fas fa-wallet",
                url: "/cartera",
                roles: ["superAdmin", "administrador"],
                divider: true,
            },
            {
                name: "Usuarios",
                icon: "fas fa-users",
                url: "/users",
                roles: ["superAdmin", "administrador"],
                divider: false,
            },
            // {
            //     name: "Roles",
            //     icon: "fas fa-tag",
            //     url: "/roles",
            //     roles: ["superAdmin"],
            //     divider: true,
            // },
        ],
    }),

    components: {
        Account,
    },

    computed: {
        isMobile() {
            return this.$vuetify.breakpoint.xsOnly;
        },
    },

    watch: {
        isMobile() {
            this.drawerControl();
        },
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
                this.mini = false;
                this.permanet = true;
                this.temporary = false;
            }
        },

        searchNavigate(route) {
            this.$router.push(route);
            this.buscar = null;
            this.clientes = [];
            this.articulos = [];
            this.negocios = [];
            this.proveedores = [];
            this.noMostrar = true;
        },

        openSide(mode) {
            this.sidenav.mode = mode;
            this.sidenav.active = true;
        },

        closeSide() {
            this.sidenav.mode = '';
            this.sidenav.active = false;
        },

        markRead(noti) {
            this.$store.dispatch("notificaciones/markRead", { id: noti.id });
            this.$router.push(`/${noti.data.action}`);
            this.sidenav.active = false;
        },
    },
};
</script>

<style lang="scss"></style>
