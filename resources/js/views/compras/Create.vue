<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/compras">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <ComprasForm></ComprasForm>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ComprasForm from "../../components/compras/ComprasForm";

export default {
    components: {
        ComprasForm
    }
};
</script>

<style>
</style>