<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <CarteraIndex :limit="limit">
                        <br />
                        <v-row justify="center" v-if="$store.state.reportes.cheques">
                            <br />
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="
                                        limit >=
                                            $store.state.reportes.cheques.total
                                    "
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </v-row>
                    </CarteraIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import CarteraIndex from "../../components/cartera/CarteraIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        CarteraIndex
    },

    mounted() {
        this.getPresupuestos();
    },

    methods: {
        getPresupuestos: async function() {
            await this.$store.dispatch("reportes/cartera", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getPresupuestos();
        }
    }
};
</script>

<style></style>
