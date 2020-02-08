<template>
    <div>
        <v-container>
            <v-tabs right slider-color="secondary" active-class="secondary--text">
                <v-tab v-if="$store.state.auth.user.rol.role != 'cliente'">Activas</v-tab>
                <v-tab>
                    <span v-if="$store.state.auth.user.rol.role != 'cliente'">Todas</span>
                    <span v-else>Cuentas</span>
                </v-tab>
                <v-tab>Recibos</v-tab>
                <!-- Cuentas Activas -->
                <v-tab-item
                    style="background: white !important;"
                    v-if="$store.state.auth.user.rol.role != 'cliente'"
                >
                    <v-col cols="12">
                        <h3 class="text-saldo">U$D Saldo: {{ saldo }}</h3>
                        <h3 class="text-saldo">U$D Haber: {{ $store.state.clientes.cliente.haber }}</h3>
                    </v-col>
                    <ClientesPagos></ClientesPagos>
                </v-tab-item>
                <!-- Todas las Cuentas -->
                <v-tab-item style="background: white !important;">
                    <v-col cols="12">
                        <h3 class="text-saldo">U$D Saldo: {{ saldo }}</h3>
                        <h3 class="text-saldo">U$D Haber: {{ $store.state.clientes.cliente.haber }}</h3>
                    </v-col>
                    <ClientesCuentas></ClientesCuentas>
                </v-tab-item>
                <!-- Recibos -->
                <v-tab-item style="background: white !important;">
                    <v-col cols="12">
                        <h3 class="text-saldo">U$D Saldo: {{ saldo }}</h3>
                        <h3 class="text-saldo">U$D Haber: {{ $store.state.clientes.cliente.haber }}</h3>
                    </v-col>
                    <ClientesRecibos></ClientesRecibos>
                </v-tab-item>
            </v-tabs>
        </v-container>
    </div>
</template>

<script>
import ClientesPagos from "./ClientesPagos";
import ClientesCuentas from "./ClientesCuentas";
import ClientesRecibos from "./ClientesRecibos";

export default {
    components: {
        ClientesPagos,
        ClientesCuentas,
        ClientesRecibos
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
    }
};
</script>

<style>
.text-saldo {
    display: inline;
    color: black;
    margin-left: 6px;
}
</style>