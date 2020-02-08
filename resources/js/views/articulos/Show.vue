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
                    to="/articulos"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ArticulosShow :id="id"></ArticulosShow>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ArticulosShow from "../../components/articulos/ArticulosShow";

export default {
    props: ["id"],

    components: {
        ArticulosShow
    }
};
</script>

<style>
</style>