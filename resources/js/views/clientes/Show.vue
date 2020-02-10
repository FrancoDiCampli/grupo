<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/clientes">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ClientesShow :id="id"></ClientesShow>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ClientesShow from "../../components/clientes/ClientesShow";

export default {
    props: ["id"],

    components: {
        ClientesShow
    }
};
</script>

<style></style>
