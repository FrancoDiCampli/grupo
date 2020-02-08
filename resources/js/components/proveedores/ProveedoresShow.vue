<template>
    <div>
        <v-row justify="center">
            <div v-if="$store.state.inProcess">
                <v-row justify="center" style="margin-top: 200px;">
                    <v-progress-circular :size="70" :width="7" color="primary" indeterminate></v-progress-circular>
                </v-row>
            </div>
            <v-col cols="12" v-else>
                <v-card v-if="$store.state.proveedores.proveedor" shaped>
                    <div v-if="mode == 'edit'">
                        <v-card-title>Editar Proveedor</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-form ref="proveedoresEditForm" @submit.prevent="updateProveedor()">
                                <ProveedoresForm mode="edit" ref="proveedoresForm"></ProveedoresForm>
                                <v-row justify="center">
                                    <v-btn
                                        tile
                                        @click="mode = 'show'"
                                        outlined
                                        color="secondary"
                                        class="mx-2"
                                    >Cancelar</v-btn>
                                    <v-btn tile type="submit" color="secondary" class="mx-2">Editar</v-btn>
                                </v-row>
                                <br />
                            </v-form>
                        </v-card-text>
                    </div>
                    <div v-else>
                        <div>
                            <br />
                            <div>
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
                            <v-alert
                                :value="true"
                                color="error"
                                tile
                                style="border-bottom-right-radius: 24px;"
                            >
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
                                        class="mx-2 red--text"
                                        color="white"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        tile
                                        @click="deleteProveedor()"
                                        outlined
                                        color="white"
                                        class="mx-2"
                                    >Eliminar</v-btn>
                                </v-row>
                                <br />
                            </v-alert>
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

    mounted() {
        this.getProveedor();
    },

    methods: {
        getProveedor() {
            this.$store.dispatch("proveedores/show", {
                id: this.id
            });
        },

        editProveedor() {
            this.$store
                .dispatch("proveedores/edit", {
                    data: this.$store.state.proveedores.proveedor.proveedor
                })
                .then(() => {
                    this.mode = "edit";
                });
        },

        updateProveedor: async function() {
            if (this.$refs.proveedoresEditForm.validate()) {
                this.$refs.proveedoresForm.getProvincia();
                this.$refs.proveedoresForm.getLocalidad();
                let id = this.$store.state.proveedores.form.id;

                await this.$store.dispatch("proveedores/update", {
                    id: id
                });
                await this.$store.dispatch("proveedores/show", {
                    id: id
                });
                this.mode = "show";
                this.$store.dispatch("proveedores/index");
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