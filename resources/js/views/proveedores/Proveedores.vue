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
                    to="/proveedores/nuevo"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nuevo proveedor</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ProveedoresIndex></ProveedoresIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ProveedoresIndex from "../../components/proveedores/ProveedoresIndex";

export default {
    components: {
        ProveedoresIndex
    }
};
</script>

<style>
</style>