<template>
    <div>
        <v-container>
            <v-tabs right slider-color="secondary" active-class="secondary--text">
                <v-tab v-if="$store.state.auth.user.rol != 'cliente'">Activas</v-tab>
                <v-tab>
                    <span v-if="$store.state.auth.user.rol != 'cliente'">Todas</span>
                    <span v-else>Cuentas</span>
                </v-tab>
                <v-tab>Recibos</v-tab>
                <v-tab>Resumen</v-tab>
                <!-- Cuentas Activas -->
                <v-tab-item
                    style="background: white !important;"
                    v-if="$store.state.auth.user.rol != 'cliente'"
                >
                    <ClientesPagos></ClientesPagos>
                </v-tab-item>
                <!-- Todas las Cuentas -->
                <v-tab-item style="background: white !important;">
                    <ClientesCuentas></ClientesCuentas>
                </v-tab-item>
                <!-- Recibos -->
                <v-tab-item style="background: white !important;">
                    <ClientesRecibos></ClientesRecibos>
                </v-tab-item>
                <v-tab-item style="background: white !important;">
                    <ClientesResumenCuenta></ClientesResumenCuenta>
                </v-tab-item>
            </v-tabs>
        </v-container>
    </div>
</template>

<script>
import ClientesPagos from "./ClientesPagos";
import ClientesCuentas from "./ClientesCuentas";
import ClientesRecibos from "./ClientesRecibos";
import ClientesResumenCuenta from "./ClientesResumenCuenta";

export default {
    data() {
        return {
            rules: {
                required: (value) => !!value || "Este campo es obligatorio",
            },
        };
    },

    components: {
        ClientesPagos,
        ClientesCuentas,
        ClientesRecibos,
        ClientesResumenCuenta
    },
};
</script>

<style></style>
