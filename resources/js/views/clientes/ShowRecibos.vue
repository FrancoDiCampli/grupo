<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn
                    color="secondary"
                    dark
                    fab
                    fixed
                    right
                    bottom
                    large
                    v-on="on"
                    :to="`/clientes/show/${$store.state.clientes.cliente.cliente.id}`"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ClientesShowRecibos :id="id"></ClientesShowRecibos>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ClientesShowRecibos from "../../components/clientes/ClientesShowRecibos.vue";

export default {
    props: ["id"],

    components: {
        ClientesShowRecibos
    }
};
</script>

<style>
</style>