<template>
    <div>
        <v-row justify="center">
            <v-col cols="12">
                <v-card
                    v-if="$store.state.proveedores.proveedor"
                    shaped
                    outlined
                    :loading="$store.state.inProcess"
                >
                    <div v-if="mode == 'edit'">
                        <v-card-title>Editar Proveedor</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row justify="center">
                                <v-col cols="12" sm="10">
                                    <v-form
                                        ref="proveedoresEditForm"
                                        @submit.prevent="updateProveedor()"
                                    >
                                        <ProveedoresForm mode="edit" ref="proveedoresForm"></ProveedoresForm>
                                        <v-row justify="center">
                                            <v-btn
                                                tile
                                                @click="mode = 'show'"
                                                outlined
                                                :disabled="$store.state.inProcess"
                                                color="secondary"
                                                class="mx-2"
                                            >Cancelar</v-btn>
                                            <v-btn
                                                tile
                                                type="submit"
                                                color="secondary"
                                                class="mx-2 elevation-0"
                                                :disabled="$store.state.inProcess"
                                                :loading="$store.state.inProcess"
                                            >Editar</v-btn>
                                        </v-row>
                                        <br />
                                    </v-form>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </div>
                    <div v-else>
                        <div>
                            <br />
                            <div
                                v-if="$store.state.auth.user.rol == 'superAdmin' || $store.state.auth.user.rol == 'administrador'"
                            >
                                <v-menu>
                                    <template v-slot:activator="{ on }">
                                        <v-btn
                                            absolute
                                            right
                                            text
                                            icon
                                            dark
                                            color="secondary"
                                            v-on="on"
                                        >
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item @click="editProveedor()">
                                            <v-list-item-title>Editar</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="mode = 'delete'">
                                            <v-list-item-title>Eliminar</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </div>
                            <v-row justify="center">
                                <v-avatar size="160" color="secondary">
                                    <span
                                        class="white--text text-uppercase"
                                        style="font-size: 60px;"
                                    >{{ $store.state.proveedores.proveedor.proveedor.razonsocial[0] }}</span>
                                </v-avatar>
                                <br />
                                <v-col cols="12">
                                    <h1
                                        class="text-center secondary--text"
                                    >{{ $store.state.proveedores.proveedor.proveedor.razonsocial }}</h1>
                                    <h3
                                        class="text-center secondary--text"
                                    >{{ $store.state.proveedores.proveedor.proveedor.cuit }}</h3>
                                </v-col>
                            </v-row>
                        </div>
                        <br />
                        <div v-if="mode == 'show'">
                            <template>
                                <div>
                                    <v-tabs
                                        grow
                                        slider-color="secondary"
                                        active-class="secondary--text"
                                    >
                                        <v-tab>Datos</v-tab>
                                        <v-tab>Compras</v-tab>
                                        <v-tab-item style="background: white !important;">
                                            <div v-if="$store.state.proveedores.proveedor">
                                                <ProveedoresShowData></ProveedoresShowData>
                                            </div>
                                        </v-tab-item>
                                        <v-tab-item style="background: white !important;">
                                            <ProveedoresShowCompras></ProveedoresShowCompras>
                                        </v-tab-item>
                                    </v-tabs>
                                </div>
                                <br />
                            </template>
                        </div>
                        <div v-else-if="mode == 'delete'">
                            <div class="show-delete">
                                <h2 class="text-center white--text">¿Estas Seguro?</h2>
                                <br />
                                <v-divider dark></v-divider>
                                <br />
                                <p
                                    class="text-center white--text"
                                >¿Realmente deseas eliminar este Proveedor?</p>
                                <p class="text-center white--text">Este Cambio es Irreversible</p>
                                <br />
                                <v-row justify="center">
                                    <v-btn
                                        @click="mode = 'show'"
                                        tile
                                        class="mx-2 red--text elevation-0"
                                        color="white"
                                        :disabled="$store.state.inProcess"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        tile
                                        @click="deleteProveedor()"
                                        outlined
                                        color="white"
                                        class="mx-2"
                                        :disabled="$store.state.inProcess"
                                        :loading="$store.state.inProcess"
                                    >Eliminar</v-btn>
                                </v-row>
                                <br />
                            </div>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ProveedoresForm from "./ProveedoresForm";
import ProveedoresShowData from "./ProveedoresShowData";
import ProveedoresShowCompras from "./ProveedoresShowCompras";

export default {
    name: "ArticulosShow",

    data() {
        return {
            mode: "show"
        };
    },

    props: ["id"],

    components: {
        ProveedoresForm,
        ProveedoresShowData,
        ProveedoresShowCompras
    },

    watch: {
        mode() {
            if (this.mode == "show") {
                let provinciaState = this.$store.state.proveedores.proveedor
                    .proveedor.provincia;
                let localidadState = this.$store.state.proveedores.proveedor
                    .proveedor.localidad;

                if (typeof provinciaState == "object") {
                    this.$store.state.proveedores.proveedor.proveedor.provincia =
                        provinciaState.nombre;
                }

                if (typeof localidadState == "object") {
                    this.$store.state.proveedores.proveedor.proveedor.localidad =
                        localidadState.nombre;
                }
            }
        }
    },

    mounted() {
        this.getProveedor();
    },

    methods: {
        getProveedor() {
            this.$store.dispatch("proveedores/show", {
                id: this.id
            });
        },

        editProveedor: async function() {
            await this.$store.dispatch("proveedores/edit", {
                data: this.$store.state.proveedores.proveedor.proveedor
            });

            this.mode = "edit";
        },

        updateProveedor: async function() {
            if (this.$refs.proveedoresEditForm.validate()) {
                await this.$refs.proveedoresForm.getProvincia();
                await this.$refs.proveedoresForm.getLocalidad();
                let id = this.$store.state.proveedores.form.id;
                await this.$store.dispatch("proveedores/update", {
                    id: id
                });
                await this.$store.dispatch("proveedores/show", {
                    id: id
                });
                this.mode = "show";
            }
        },

        deleteProveedor: async function() {
            await this.$store.dispatch("proveedores/destroy", {
                id: this.$store.state.proveedores.proveedor.proveedor.id
            });
            await this.$store.dispatch("proveedores/index");
            this.mode = "show";
            this.$router.push("/proveedores");
        }
    }
};
</script>

<style>
</style>