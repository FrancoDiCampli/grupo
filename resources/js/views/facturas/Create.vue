<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/ventas">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <FacturasForm></FacturasForm>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import FacturasForm from "../../components/facturas/FacturasForm";

export default {
    components: {
        FacturasForm
    }
};
</script>

<style>
</style>