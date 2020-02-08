<template>
    <div>
        <v-row justify="center">
            <v-col cols="12">
                <v-card
                    v-if="$store.state.sucursales.sucursal"
                    shaped
                    outlined
                    :loading="$store.state.inProcess"
                >
                    <div v-if="mode == 'edit'">
                        <v-card-title>Editar sucursal</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row justify="center">
                                <v-col cols="12" sm="10">
                                    <v-form
                                        ref="sucursalesEditForm"
                                        @submit.prevent="updateSucursal()"
                                    >
                                        <SucursalesForm mode="edit" ref="SucursalesForm"></SucursalesForm>
                                        <v-row justify="center">
                                            <v-btn
                                                tile
                                                text
                                                @click="mode = 'show'"
                                                outlined
                                                color="secondary"
                                                class="mx-2"
                                                :disabled="
                                                    $store.state.inProcess
                                                "
                                            >Cancelar</v-btn>
                                            <v-btn
                                                tile
                                                type="submit"
                                                color="secondary"
                                                class="mx-2 elevation-0"
                                                :disabled="
                                                    $store.state.inProcess
                                                "
                                                :loading="
                                                    $store.state.inProcess
                                                "
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
                                        <v-list-item @click="editSucursal()">
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
                                    >
                                        {{
                                        $store.state.sucursales.sucursal
                                        .negocio.nombre[0]
                                        }}
                                    </span>
                                </v-avatar>
                                <br />
                                <v-col cols="12">
                                    <h1 class="text-center secondary--text">
                                        {{
                                        $store.state.sucursales.sucursal
                                        .negocio.nombre
                                        }}
                                    </h1>
                                    <h3 class="text-center secondary--text">
                                        {{
                                        $store.state.sucursales.sucursal
                                        .negocio.direccion
                                        }}
                                        -
                                        {{
                                        $store.state.sucursales.sucursal
                                        .negocio.localidad
                                        }}
                                        -
                                        {{
                                        $store.state.sucursales.sucursal
                                        .negocio.provincia
                                        }}
                                    </h3>
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
                                        <v-tab>Ventas</v-tab>
                                        <v-tab-item style="background: white !important;">
                                            <div
                                                v-if="
                                                    $store.state.sucursales
                                                        .sucursal
                                                "
                                            >
                                                <SucursalesShowData></SucursalesShowData>
                                            </div>
                                        </v-tab-item>
                                        <v-tab-item style="background: white !important;">
                                            <SucursalesShowVentas></SucursalesShowVentas>
                                        </v-tab-item>
                                    </v-tabs>
                                </div>
                                <br />
                            </template>
                        </div>
                        <div v-else-if="mode == 'delete'">
                            <div class="sucursal-delete">
                                <h2 class="text-center white--text">¿Estas Seguro?</h2>
                                <br />
                                <v-divider dark></v-divider>
                                <br />
                                <p
                                    class="text-center white--text"
                                >¿Realmente deseas eliminar este Negocio?</p>
                                <p class="text-center white--text">Este Cambio es Irreversible</p>
                                <br />
                                <v-row justify="center">
                                    <v-btn
                                        @click="mode = 'show'"
                                        :disabled="$store.state.inProcess"
                                        tile
                                        class="mx-2 red--text elevation-0"
                                        color="white"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        tile
                                        @click="deleteSucursal()"
                                        :disabled="$store.state.inProcess"
                                        :loading="$store.state.inProcess"
                                        outlined
                                        color="white"
                                        class="mx-2"
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
import SucursalesForm from "./SucursalesForm";
import SucursalesShowData from "./SucursalesShowData";
import SucursalesShowVentas from "./SucursalesShowVentas";

export default {
    data() {
        return {
            mode: "show"
        };
    },

    props: ["id"],

    components: {
        SucursalesForm,
        SucursalesShowData,
        SucursalesShowVentas
    },

    watch: {
        mode() {
            if (this.mode == "show") {
                let provinciaState = this.$store.state.sucursales.sucursal
                    .negocio.provincia;
                let localidadState = this.$store.state.sucursales.sucursal
                    .negocio.localidad;

                if (typeof provinciaState == "object") {
                    this.$store.state.sucursales.sucursal.negocio.provincia =
                        provinciaState.nombre;
                }

                if (typeof localidadState == "object") {
                    this.$store.state.sucursales.sucursal.negocio.localidad =
                        localidadState.nombre;
                }
            }
        }
    },

    mounted() {
        this.getSucursal();
    },

    methods: {
        getSucursal() {
            this.$store.dispatch("sucursales/show", {
                id: this.id
            });
        },

        editSucursal: async function() {
            await this.$store.dispatch("sucursales/edit", {
                data: this.$store.state.sucursales.sucursal.negocio
            });
            this.mode = "show";
        },

        updateSucursal: async function() {
            if (this.$refs.sucursalesEditForm.validate()) {
                this.$refs.SucursalesForm.getProvincia();
                this.$refs.SucursalesForm.getLocalidad();
                let id = this.$store.state.sucursales.form.id;
                await this.$store.dispatch("sucursales/update", {
                    id: id
                });
                await this.$store.dispatch("sucursales/show", {
                    id: id
                });
                this.mode = "show";
            }
        },

        deleteSucursal: async function() {
            await this.$store.dispatch("sucursales/destroy", {
                id: this.$store.state.sucursales.sucursal.negocio.id
            });
            this.mode = "show";
            this.$router.push("/sucursales");
        }
    }
};
</script>

<style lang="scss">
.sucursal-delete {
    padding-top: 32px;
    border-bottom-right-radius: 24px;
    background-color: #f44336;
}
</style>
