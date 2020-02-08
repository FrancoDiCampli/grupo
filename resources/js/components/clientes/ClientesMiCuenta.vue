<template>
    <div>
        <v-row justify="center">
            <div v-if="inProcess">
                <v-row justify="center" style="margin-top: 200px;">
                    <v-progress-circular :size="70" :width="7" color="primary" indeterminate></v-progress-circular>
                </v-row>
            </div>
            <v-col cols="12" v-else>
                <v-card v-if="$store.state.clientes.cliente" shaped>
                    <div>
                        <br />
                        <v-row justify="center">
                            <v-avatar size="160" color="secondary">
                                <span
                                    class="white--text text-uppercase"
                                    style="font-size: 60px;"
                                >{{ $store.state.clientes.cliente.cliente.razonsocial[0] }}</span>
                            </v-avatar>
                            <br />
                            <v-col cols="12">
                                <h1
                                    class="text-center secondary--text"
                                >{{ $store.state.clientes.cliente.cliente.razonsocial }}</h1>
                                <h3
                                    class="text-center secondary--text"
                                >{{ $store.state.clientes.cliente.cliente.documentounico }}</h3>
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
                                    <v-tab>Compras</v-tab>
                                    <v-tab>
                                        <span class="hidden-sm-and-up">Cuenta</span>
                                        <span class="hidden-xs-only">Resumen Corriente</span>
                                    </v-tab>
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