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

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <VentasShow :id="id"></VentasShow>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import VentasShow from "../../components/ventas/VentasShow";

export default {
    props: ["id"],

    components: {
        VentasShow
    }
};
</script>

<style>