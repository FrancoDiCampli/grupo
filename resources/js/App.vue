<template>
    <v-app :style="{ background: $vuetify.theme.themes[theme].background }">
        <AppBar>
            <template slot="userActions">
                <v-list-item @click="exit()">
                    <v-list-item-title>Cerrar sesión</v-list-item-title>
                </v-list-item>
            </template>
            <template slot="searchBar" v-if="$store.state.auth.user">
                <div class="search-input" v-click-outside="closeSearch">
                    <v-row class="pa-0" justify="center" align-content="center">
                        <v-text-field
                            hide-details
                            loading
                            light
                            placeholder="Buscar"
                            @focus="searchOnFocus = true"
                            :class="
                            searchOnFocus ? 'searchBar focusBar' : 'searchBar'
                        "
                            v-model="searchItems"
                            class="search-field"
                        >
                            <template v-slot:progress>
                                <v-progress-linear absolute height="0"></v-progress-linear>
                            </template>
                        </v-text-field>
                        <!-- <div class="px-2 py-1" @click="searchItemsAfter()" style="cursor: pointer;">
                            <v-icon size="large">fas fa-search</v-icon>
                        </div>-->
                    </v-row>
                </div>
            </template>
        </AppBar>
        <v-content>
            <div class="searchContainer" v-if="searchDialog">
                <div class="flexSearch">
                    <v-card>
                        <v-card-text v-if="searchInProcess">
                            <v-row justify="center">
                                <v-progress-circular
                                    :size="70"
                                    :width="7"
                                    color="primary"
                                    indeterminate
                                    style="margin: 32px 0 32px 0;"
                                ></v-progress-circular>
                            </v-row>
                        </v-card-text>
                        <v-card-text v-else>
                            <div v-if="haveItems">
                                <div v-if="items.clientes.length > 0">
                                    <v-row justify="center">
                                        <v-col cols="4" sm="3">Clientes</v-col>
                                        <v-col
                                            cols="8"
                                            sm="9"
                                            class="pa-0 ma-0"
                                            style="border-left: thin solid #e0e0e0;"
                                        >
                                            <v-list dense>
                                                <v-list-item
                                                    v-for="(cliente,
                                                    index) in items.clientes"
                                                    :key="index"
                                                    @click="
                                                        navigate(
                                                            '/clientes/show/' +
                                                                cliente.id
                                                        )
                                                    "
                                                >
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{
                                                            cliente.razonsocial
                                                            }}
                                                        </v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </v-col>
                                    </v-row>
                                </div>
                                <div v-if="items.articulos.length > 0">
                                    <v-divider></v-divider>
                                    <v-row justify="center">
                                        <v-col cols="4" sm="3">Articulos</v-col>
                                        <v-col
                                            cols="8"
                                            sm="9"
                                            class="pa-0 ma-0"
                                            style="border-left: thin solid #e0e0e0;"
                                        >
                                            <v-list dense>
                                                <v-list-item
                                                    v-for="(articulo,
                                                    index) in items.articulos"
                                                    :key="index"
                                                    @click="
                                                        navigate(
                                                            '/articulos/show/' +
                                                                articulo.id
                                                        )
                                                    "
                                                >
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{
                                                            articulo.articulo
                                                            }}
                                                        </v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </v-col>
                                    </v-row>
                                </div>
                                <div v-if="items.distribuidores.length > 0">
                                    <v-divider></v-divider>
                                    <v-row justify="center">
                                        <v-col cols="4" sm="3">Distribuidores</v-col>
                                        <v-col
                                            cols="8"
                                            sm="9"
                                            class="pa-0 ma-0"
                                            style="border-left: thin solid #e0e0e0;"
                                        >
                                            <v-list dense>
                                                <v-list-item
                                                    v-for="(distribuidor,
                                                    index) in items.distribuidores"
                                                    :key="index"
                                                    @click="
                                                        navigate(
                                                            '/clientes/show/' +
                                                                distribuidor.id
                                                        )
                                                    "
                                                >
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{
                                                            distribuidor.razonsocial
                                                            }}
                                                        </v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </v-col>
                                    </v-row>
                                </div>
                                <div v-if="items.proveedores.length > 0">
                                    <v-divider></v-divider>
                                    <v-row justify="center">
                                        <v-col cols="4" sm="3">Proveedores</v-col>
                                        <v-col
                                            cols="8"
                                            sm="9"
                                            class="pa-0 ma-0"
                                            style="border-left: thin solid #e0e0e0;"
                                        >
                                            <v-list dense>
                                                <v-list-item
                                                    v-for="(proveedor,
                                                    index) in items.proveedores"
                                                    :key="index"
                                                    @click="
                                                        navigate(
                                                            '/proveedores/show/' +
                                                                proveedor.id
                                                        )
                                                    "
                                                >
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{
                                                            proveedor.razonsocial
                                                            }}
                                                        </v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </v-col>
                                    </v-row>
                                </div>
                            </div>
                            <div v-else class="py-5">
                                <h3 class="text-center">
                                    Ningun dato coincide con lel criterio de
                                    busqueda
                                </h3>
                            </div>
                        </v-card-text>
                    </v-card>
                </div>
            </div>

            <v-container>
                <v-row justify="center" v-if="process">
                    <v-progress-circular
                        :size="70"
                        :width="7"
                        color="primary"
                        indeterminate
                        style="margin-top: 250px;"
                    ></v-progress-circular>
                </v-row>
                <div v-else>
                    <Errors></Errors>
                    <router-view></router-view>
                </div>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import ClickOutside from "v-click-outside";

import AppBar from "./components/AppBar";
import Errors from "./components/Errors";

export default {
    data: () => ({
        searchOnFocus: false,
        process: false,
        searchItems: null,
        searchInProcess: false,
        searchItemsList: false,
        searchDialog: false,
        items: {
            clientes: [],
            proveedores: [],
            articulos: [],
            distribuidores: [],
        },
    }),

    components: {
        AppBar,
        Errors,
    },

    directives: {
        clickOutside: ClickOutside.directive,
    },

    computed: {
        theme() {
            return this.$vuetify.theme.dark ? "dark" : "light";
        },

        haveItems() {
            if (this.$store.state.auth.user) {
                if (
                    this.$store.state.auth.user.rol == "administrador" ||
                    this.$store.state.auth.user.rol == "superAdmin"
                ) {
                    if (this.items) {
                        if (
                            this.items.clientes.length > 0 ||
                            this.items.proveedores.length > 0 ||
                            this.items.articulos.length > 0 ||
                            this.items.distribuidores.length > 0
                        ) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else if (this.$store.state.auth.user.rol == "vendedor") {
                    if (this.items) {
                        if (
                            this.items.clientes.length > 0 ||
                            this.items.articulos.length > 0
                        ) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        },
    },

    mounted() {
        if (JSON.parse(window.localStorage.getItem("logged"))) {
            this.recoverSession();
        }
    },

    methods: {
        recoverSession() {
            this.process = true;
            this.$store
                .dispatch("auth/user")
                .then((response) => {
                    response.permissions.push("authenticated");
                    this.$user.set({
                        rol: response.rol,
                        permissions: response.permissions,
                    });
                    this.process = false;
                })
                .catch((error) => {
                    this.process = false;
                });
        },

        searchItemsAfter() {
            this.searchDialog = true;
            this.searchInProcess = true;
            this.searchItemsList = true;
            if (this.searchItems != null && this.searchItems != "") {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {
                    this.findItems();
                }, 1000);
            }
        },

        findItems: async function () {
            axios
                .post("/api/buscando", { buscar: this.searchItems })
                .then((response) => {
                    this.items = {
                        proveedores: response.data.proveedores || [],
                        clientes: response.data.clientes || [],
                        distribuidores: response.data.distribuidores || [],
                        articulos: response.data.articulos || [],
                    };
                    this.searchInProcess = false;
                })
                .catch((error) => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        closeSearch() {
            this.searchDialog = false;
            this.searchOnFocus = false;
            this.searchItems = null;
            this.searchItemsList = false;
            this.searchInProcess = false;
            this.items = {
                clientes: [],
                proveedores: [],
                articulos: [],
            };
        },

        navigate(route) {
            this.$router.push(route);
            this.closeSearch();
        },

        exit: async function () {
            this.process = true;
            await this.$store.dispatch("auth/logout");
            this.$router.push("/"); // BORRAR CUANDO SE DEFINAN LOS PERMISOS Y ROLES EN LAS RUTAS
            this.process = false;
        },
    },
};
</script>

<style lang="scss">
body::-webkit-scrollbar {
    width: 6px;
}
body::-webkit-scrollbar-thumb {
    background-color: #44c2f7;
}

.search-field {
    .v-input__control {
        .v-input__slot {
            .v-text-field__slot {
                input {
                    padding-left: 12px;
                }
            }
        }
    }
}
</style>
