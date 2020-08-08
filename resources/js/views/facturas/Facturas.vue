<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <FacturasIndex @erase="restart()">
                        <div v-if="$store.state.facturas.facturas">
                            <br />
                            <v-row justify="center" v-if="$store.state.pedidos.pedidos">
                                <br />
                                <v-btn
                                    :loading="$store.state.inProcess"
                                    :disabled="limit >= $store.state.facturas.facturas.total"
                                    @click="loadMore()"
                                    color="secondary"
                                    outlined
                                    tile
                                >Cargar Más</v-btn>
                            </v-row>
                        </div>
                    </FacturasIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import FacturasIndex from "../../components/facturas/FacturasIndex";

export default {
    data: () => ({
        limit: 10,
    }),

    components: {
        FacturasIndex,
    },

    mounted() {
        this.getFacturas();
    },

    methods: {
        getFacturas: async function () {
            await this.$store.dispatch("facturas/index", {
                limit: this.limit,
            });
        },

        loadMore: async function () {
            this.limit += 10;
            await this.getFacturas();
        },

        // TODO: Añadir en pedidos y facturas
        restart() {
            this.getFacturas();
        },
    },
};
</script>

<style></style>