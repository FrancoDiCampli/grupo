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
                    to="/consignaciones/nueva"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva consignacion</span>
        </v-tooltip>
        <ConsignacionesIndex></ConsignacionesIndex>
    </div>
</template>

<script>
import ConsignacionesIndex from "../../components/consignaciones/ConsignacionesIndex";

export default {
    components: {
        ConsignacionesIndex
    },
    mounted() {
        this.getConsignaciones();
    },

    methods: {
        getConsignaciones: async function() {
            await this.$store.dispatch("consignaciones/index");
        }
    }
};
</script>

<style>
</style>