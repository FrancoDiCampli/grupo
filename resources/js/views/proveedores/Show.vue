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
                    to="/proveedores"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ProveedoresShow :id="id"></ProveedoresShow>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ProveedoresShow from "../../components/proveedores/ProveedoresShow";

export default {
    props: ["id"],

    components: {
        ProveedoresShow
    }
};
</script>

<style>
</style>