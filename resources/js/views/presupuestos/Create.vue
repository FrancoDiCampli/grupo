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
                    to="/presupuestos"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <PresupuestosForm mode="create"></PresupuestosForm>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import PresupuestosForm from "../../components/presupuestos/PresupuestosForm";

export default {
    components: {
        PresupuestosForm
    }
};
</script>

<style></style>
