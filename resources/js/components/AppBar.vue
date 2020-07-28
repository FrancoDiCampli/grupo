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
            v-if="$store.state.auth.user.rol != 'cliente'"
        >
            <v-list-item class="drawer-action primary" dark>
                <v-list-item-icon @click="mini = !mini" class="drawer-action-icon hidden-xs-only">
                    <v-icon v-if="mini">fas fa-bars</v-icon>
                    <v-icon v-else style="margin-left: 6px;">fas fa-times</v-icon>
                </v-list-item-icon>
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
                    <v-divider v-if="route.divider"></v-divider>
                </div>
            </v-list>
        </v-navigation-drawer>

        <!-- Drawer Notificaciones -->
        <v-navigation-drawer v-model="notificationDrawer" absolute temporary right>
            <v-list dense>
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
            <v-divider></v-divider>
            <v-list dense disabled>
                <v-list-item v-for="noti in $store.state.notificaciones.read" :key="noti.id">
                    <v-list-item-icon>
                        <v-icon>fas fa-box-open</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>{{ noti.data.message }}</v-list-item-title>
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

            <!-- Titulo de la aplicacion -->
            <v-toolbar-title class="hidden-xs-only">Grupo APC</v-toolbar-title>

            <slot name="searchBar"></slot>
            <v-spacer></v-spacer>
            <!-- Notificaciones -->

            <v-btn icon @click="notificationDrawer = true" class="mx-2">
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
                    <v-list-item @click="sidenav = true">
                        <v-list-item-title>Perfil</v-list-item-title>
                    </v-list-item>
                    <slot name="userActions"></slot>
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
        notificationDrawer: false,
        clientes: [],
        proveedores: [],
        articulos: [],
        negocios: [],
        drawer: false,
        mini: false,
        permanent: false,
        temporary: true,
        sidenav: false,
        routes: [
            {
                name: "Nota de pedido",
                icon: "fas fa-receipt",
                url: "/pedidos",
                roles: [
                    "superAdmin",
                    "administrador",
                    "vendedor",
                    "distribuidor"
                ],
                divider: false
            },
            {
                name: "Ventas",
                icon: "fas fa-dollar-sign",
                url: "/ventas",
                roles: [
                    "superAdmin",
                    "administrador",
                    "vendedor",
                    "distribuidor"
                ],
                divider: true
            },

            {
                name: "Clientes",
                icon: "fas fa-user-friends",
                url: "/clientes",
                roles: [
                    "superAdmin",
                    "administrador",
                    "vendedor",
                    "distribuidor"
                ],
                divider: false
            },

            {
                name: "Articulos",
                icon: "fas fa-box-open",
                url: "/articulos",
                roles: [
                    "superAdmin",
                    "administrador",
                    "vendedor",
                    "distribuidor"
                ],
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
                divider: false
            },
            {
                name: "Consignaciones",
                icon: "fas fa-file-signature",
                url: "/consignaciones",
                roles: ["superAdmin", "administrador"],
                divider: false
            },
            {
                name: "Devoluciones",
                icon: "fas fa-exchange-alt",
                url: "/devoluciones",
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

        searchNavigate(route) {
            this.$router.push(route);
            this.buscar = null;
            this.clientes = [];
            this.articulos = [];
            this.negocios = [];
            this.proveedores = [];
            this.noMostrar = true;
        },

        markRead(noti) {
            this.$store.dispatch("notificaciones/markRead", { id: noti.id });
            this.$router.push(noti.data.action);
        }
    }
};
</script>

<style lang="scss"></style>
