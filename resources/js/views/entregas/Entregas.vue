<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <EntregasIndex @erase="restart()">
                        <div v-if="$store.state.entregas.entregas">
                            <br />
                            <v-row justify="center" v-if="$store.state.entregas.entregas">
                                <br />
                                <v-btn
                                    :loading="$store.state.inProcess"
                                    :disabled="limit >= $store.state.entregas.entregas.total"
                                    @click="loadMore()"
                                    color="secondary"
                                    outlined
                                    tile
                                >Cargar Más</v-btn>
                            </v-row>
                        </div>
                    </EntregasIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import EntregasIndex from "../../components/entregas/EntregasIndex";

export default {
    data: () => ({
        limit: 10,
    }),

    components: {
        EntregasIndex,
    },

    mounted() {
        this.getEntregas();
    },

    methods: {
        getEntregas: async function () {
            await this.$store.dispatch("entregas/index", {
                limit: this.limit,
            });
        },

        loadMore: async function () {
            this.limit += 10;
            await this.getEntregas();
        },

        // TODO: Añadir en pedidos y facturas
        restart() {
            this.getFacturas();
        },
    },
};
</script>

<style></style>