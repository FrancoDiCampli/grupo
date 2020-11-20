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
                    to="devoluciones/nueva"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva devolución</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <DevolucionesIndex></DevolucionesIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import DevolucionesIndex from "../../components/devoluciones/DevolucionesIndex";

export default {
    data: () => ({
        // limit: 10
    }),

    components: {
        DevolucionesIndex
    },

    mounted() {
        this.getDevoluciones();
    },

    methods: {
        getDevoluciones: async function() {
            await this.$store.dispatch("devoluciones/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getDevoluciones();
        }
    }
};
</script>

<style></style>
