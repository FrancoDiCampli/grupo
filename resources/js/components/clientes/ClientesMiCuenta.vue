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
                    <div>
                        <br />
                        <v-row justify="center">
                            <v-avatar size="160" color="secondary">
                                <span class="white--text text-uppercase" style="font-size: 60px;">
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
                                    <h3 :class="saldo > 0 ? 'error--text' : 'secondary--text'">U$D Saldo: {{ saldo | formatCurrency('USD') }}</h3>
                                    <h3
                                        class="secondary--text"
                                    >U$D Haber: {{ $store.state.clientes.cliente.haber | formatCUrrency('USD') }}</h3>
                                </v-row>
                            </v-col>
                        </v-row>
                    </div>
                    <br />
                    <div>
                        <template>
                            <div>
                                <v-tabs
                                    grow
                                    slider-color="secondary"
                                    active-class="secondary--text"
                                >
                                    <v-tab>Mis compras</v-tab>
                                    <v-tab>Mi Cuenta</v-tab>
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
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ClientesForm from "./ClientesForm";
import ClientesShowVentas from "./ClientesShowVentas";
import ClientesShowCuentas from "./ClientesShowCuentas";

export default {
    data() {
        return {
            inProcess: false
        };
    },

    components: {
        ClientesForm,
        ClientesShowVentas,
        ClientesShowCuentas
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
            this.inProcess = true;
            await this.$store.dispatch("clientes/miCuenta");
            this.inProcess = false;
        }
    }
};
</script>

<style>
</style>