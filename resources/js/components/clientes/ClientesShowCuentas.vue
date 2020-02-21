<template>
    <div>
        <v-container>
            <v-tabs
                right
                slider-color="secondary"
                active-class="secondary--text"
            >
                <v-tab v-if="$store.state.auth.user.rol != 'cliente'"
                    >Activas</v-tab
                >
                <v-tab>
                    <span v-if="$store.state.auth.user.rol != 'cliente'"
                        >Todas</span
                    >
                    <span v-else>Cuentas</span>
                </v-tab>
                <v-tab>Recibos</v-tab>
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
    }
};
</script>

<style></style>
