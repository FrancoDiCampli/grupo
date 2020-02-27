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
                    to="/ventas/nueva"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva venta</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <VentasIndex>
                        <div v-if="$store.state.ventas.ventas">
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="limit >= $store.state.ventas.ventas.total"
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </div>
                    </VentasIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import VentasIndex from "../../components/ventas/VentasIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        VentasIndex
    },

    watch: {
        sucursalID() {
            this.getVentas();
            this.getFacturas();
        }
    },

    mounted() {
        this.getVentas();
        this.getFacturas();
    },

    methods: {
        getVentas: async function() {
            await this.$store.dispatch("ventas/index", {
                limit: this.limit
            });
        },

        getFacturas: async function() {
            await this.$store.dispatch("facturas/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += 10;
            await this.getVentas();
            await this.getFacturas();
        }
    }
};
</script>

<style></style>
