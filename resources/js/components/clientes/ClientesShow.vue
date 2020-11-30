<template>
    <div>
        <v-row justify="center">
            <v-col cols="12">
                <v-card
                    v-if="$store.state.clientes.cliente"
                    shaped
                    outlined
                    :loading="$store.state.inProcess"
                >
                    <div v-if="mode == 'edit'">
                        <v-card-title>Editar cliente</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row justify="center">
                                <v-col cols="12" sm="10">
                                    <v-form
                                        ref="clientesEditForm"
                                        @submit.prevent="updateCliente()"
                                    >
                                        <ClientesForm mode="edit" ref="clientesForm"></ClientesForm>
                                        <v-row justify="center">
                                            <v-btn
                                                tile
                                                :disabled="
                                                    $store.state.inProcess
                                                "
                                                @click="mode = 'show'"
                                                outlined
                                                color="secondary"
                                                class="mx-2"
                                            >Cancelar</v-btn>
                                            <v-btn
                                                tile
                                                :loading="
                                                    $store.state.inProcess
                                                "
                                                :disabled="
                                                    $store.state.inProcess
                                                "
                                                type="submit"
                                                color="secondary"
                                                class="mx-2 elevation-0"
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
                                v-if="
                                    $store.state.auth.user.rol ==
                                        'superAdmin' ||
                                        $store.state.auth.user.rol ==
                                            'administrador'
                                "
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
                                        <v-list-item @click="editCliente()">
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
                                        $store.state.clientes.cliente
                                        .cliente.razonsocial[0]
                                        }}
                                    </span>
                                </v-avatar>
                                <br />
                                <v-col cols="12">
                                    <h1 class="text-center secondary--text">
                                        {{
                                        $store.state.clientes.cliente
                                        .cliente.razonsocial
                                        }}
                                    </h1>
                                    <h3 class="text-center secondary--text">
                                        {{
                                        $store.state.clientes.cliente
                                        .cliente.documentounico
                                        }}
                                    </h3>
                                </v-col>
                                <v-col cols="10" sm="8">
                                    <v-row justify="space-between">
                                        <h3
                                            :class="saldo > 0 ? 'red--text' : 'secondary--text'"
                                        >Saldo: {{ saldo | formatCurrency('USD') }}</h3>
                                        <h3 class="secondary--text">
                                            Haber:
                                            {{
                                            $store.state.clientes.cliente
                                            .haber | formatCurrency('USD')
                                            }}
                                        </h3>
                                    </v-row>
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
                                        <v-tab>Comprobantes</v-tab>
                                        <v-tab>
                                            <span class="hidden-sm-and-up">Cuenta</span>
                                            <span class="hidden-xs-only">Resumen corriente</span>
                                        </v-tab>
                                        <v-tab-item style="background: white !important;">
                                            <div
                                                v-if="
                                                    $store.state.clientes
                                                        .cliente
                                                "
                                            >
                                                <ClientesShowData></ClientesShowData>
                                            </div>
                                        </v-tab-item>
                                        <v-tab-item style="background: white !important;">
                                            <ClientesShowVentas></ClientesShowVentas>
                                        </v-tab-item>
                                        <v-tab-item style="background: white !important;">
                                            <ClientesShowCuentas></ClientesShowCuentas>
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
                                >¿Realmente deseas eliminar este Cliente?</p>
                                <p class="text-center white--text">Este Cambio es Irreversible</p>
                                <br />
                                <v-row justify="center">
                                    <v-btn
                                        @click="mode = 'show'"
                                        tile
                                        :disabled="$store.state.inProcess"
                                        class="mx-2 red--text elevation-0"
                                        color="white"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        tile
                                        :loading="$store.state.inProcess"
                                        :disabled="$store.state.inProcess"
                                        @click="deleteCliente()"
                                        outlined
                                        color="white "
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
import ClientesForm from "./ClientesForm";
import ClientesShowData from "./ClientesShowData";
import ClientesShowVentas from "./ClientesShowVentas";
import ClientesShowCuentas from "./ClientesShowCuentas";

export default {
    data: () => ({
        mode: "show"
    }),

    props: ["id"],

    components: {
        ClientesForm,
        ClientesShowData,
        ClientesShowVentas,
        ClientesShowCuentas
    },

    watch: {
        mode() {
            if (this.mode == "show") {
                let provinciaState = this.$store.state.clientes.cliente.cliente
                    .provincia;
                let localidadState = this.$store.state.clientes.cliente.cliente
                    .localidad;

                if (typeof provinciaState == "object") {
                    this.$store.state.clientes.cliente.cliente.provincia =
                        provinciaState.nombre;
                }

                if (typeof localidadState == "object") {
                    this.$store.state.clientes.cliente.cliente.localidad =
                        localidadState.nombre;
                }
            }
        }
    },

    computed: {
        saldo() {
            let total = null;

            if (this.$store.state.clientes.cliente) {
                if (this.$store.state.clientes.cliente.cuentas.length > 0) {
                    total = 0;
                    let cuentas = this.$store.state.clientes.cliente.cuentas;

                    for (let i = 0; i < cuentas.length; i++) {
                        total = total + Number(cuentas[i].saldo);
                    }

                    return total;
                }
            }

            return total;
        }
    },

    mounted() {
        this.getCliente();
    },

    methods: {
        getCliente: async function() {
            await this.$store.dispatch("clientes/show", {
                id: this.id
            });
        },

        editCliente: async function() {
            await this.$store.dispatch("clientes/edit", {
                data: this.$store.state.clientes.cliente.cliente
            });
            this.mode = "edit";
        },

        updateCliente: async function() {
            if (this.$refs.clientesEditForm.validate()) {
                await this.$refs.clientesForm.getProvincia();
                await this.$refs.clientesForm.getLocalidad();
                let id = this.$store.state.clientes.form.id;
                await this.$store.dispatch("clientes/update", {
                    id: id
                });
                await this.$store.dispatch("clientes/show", {
                    id: id
                });
                this.mode = "show";
            }
        },

        deleteCliente: async function() {
            await this.$store.dispatch("clientes/destroy", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            this.mode = "show";
            this.$router.push("/clientes");
        }
    }
};
</script>

<style></style>
