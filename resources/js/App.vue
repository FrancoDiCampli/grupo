<template>
    <v-app :style="{ background: $vuetify.theme.themes[theme].background }">
        <AppBar>
            <template slot="userActions">
                <v-list-item @click="exit()">
                    <v-list-item-title>Cerrar sesión</v-list-item-title>
                </v-list-item>
            </template>
            <template slot="searchBar" v-if="$store.state.auth.user">
                <v-btn
                    icon
                    class="mr-3"
                    @click="searchDialog = true"
                    v-if="$store.state.auth.user.rol != 'cliente'"
                >
                    <v-icon>fas fa-search</v-icon>
                </v-btn>
            </template>
        </AppBar>
        <v-content>
            <v-dialog
                v-model="searchDialog"
                width="750px"
                :fullscreen="$vuetify.xsOnly"
                hide-overlay
            >
                <div class="pa-3" style="background-color: white;">
                    <v-text-field
                        v-model="searchItems"
                        @keyup="searchItemsAfter()"
                        placeholder="Buscar"
                        outlined
                    ></v-text-field>
                    <v-card outlined :loading="searchInProcess">
                        <v-card-text v-if="$store.state.auth.user">
                            <div v-if="haveItems">
                                <v-list>
                                    <v-subheader v-if="items.clientes">Clientes</v-subheader>
                                    <v-list-item
                                        v-for="(cliente,
                                        index) in items.clientes"
                                        :key="index"
                                        @click="
                                            navigate(
                                                '/clientes/show/' + cliente.id
                                            )
                                        "
                                    >
                                        <v-list-item-content>
                                            <v-list-item-title v-text="cliente.razonsocial"></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-subheader v-if="items.proveedores">Proveedores</v-subheader>
                                    <v-list-item
                                        v-for="(prov,
                                        index) in items.proveedores"
                                        :key="index"
                                        @click="
                                            navigate(
                                                '/proveedores/show/' + prov.id
                                            )
                                        "
                                    >
                                        <v-list-item-content>
                                            <v-list-item-title v-text="prov.razonsocial"></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-subheader v-if="items.articulos">Articulos</v-subheader>
                                    <v-list-item
                                        v-for="(articulo,
                                        index) in items.articulos"
                                        :key="index"
                                        @click="
                                            navigate(
                                                '/articulos/show/' + articulo.id
                                            )
                                        "
                                    >
                                        <v-list-item-content>
                                            <v-list-item-title v-text="articulo.articulo"></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
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
            </v-dialog>

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
        process: false,
        searchItems: null,
        searchInProcess: false,
        searchItemsList: false,
        searchDialog: false,
        items: null
    }),

    components: {
        AppBar,
        Errors
    },

    directives: {
        clickOutside: ClickOutside.directive
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
                            this.items.usuarios.length > 0
                        ) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else if (this.$store.state.auth.user.rol == "vendedor") {
                    if (this.items) {
                        if (this.items.clientes.length > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }
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
                .then(response => {
                    response.permissions.push("authenticated");
                    this.$user.set({
                        rol: response.rol,
                        permissions: response.permissions
                    });
                    this.process = false;
                })
                .catch(error => {
                    this.process = false;
                });
        },

        searchItemsAfter() {
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

        findItems: async function() {
            axios
                .post("/api/buscando", { buscar: this.searchItems })
                .then(response => {
                    console.log(response.data);
                    this.items = response.data;
                    this.searchInProcess = false;
                })
                .catch(error => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        closeSearch() {
            this.searchDialog = false;
            this.searchItems = null;
            this.searchItemsList = false;
            this.searchInProcess = false;
            this.items = false;
        },

        navigate(route) {
            this.$router.push(route);
            this.closeSearch();
        },

        exit: async function() {
            this.process = true;
            await this.$store.dispatch("auth/logout");
            this.$router.push("/"); // BORRAR CUANDO SE DEFINAN LOS PERMISOS Y ROLES EN LAS RUTAS
            this.process = false;
        }
    }
};
</script>

<style lang="scss">
body::-webkit-scrollbar {
    width: 6px;
}
body::-webkit-scrollbar-thumb {
    background-color: #44c2f7;
}

.search-items-container {
    width: 100%;
    position: fixed;
    z-index: 5;
    margin-top: -72px;
    .search-items-input {
        .v-input__control {
            height: 32px !important;
            fieldset {
                height: 40px !important;
            }

            .v-text-field__slot {
                height: 32px !important;
            }
        }
    }
}

@media (max-width: 600px) {
    .search-items-container {
        margin-top: -68px;
    }
}
</style>
