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
                    to="/pedidos/nuevo"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva Nota de Pedido</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <PedidosIndex>
                        <br />
                        <v-row justify="center" v-if="$store.state.pedidos.pedidos">
                            <br />
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="
                                        limit >=
                                            $store.state.pedidos.pedidos.total
                                    "
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </v-row>
                    </PedidosIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import PedidosIndex from "../../components/pedidos/PedidosIndex";

export default {
    data: () => ({
        limit: 10,
    }),

    components: {
        PedidosIndex,
    },

    mounted() {
        this.getPedidos();
    },

    methods: {
        getPedidos: async function () {
            await this.$store.dispatch("pedidos/index", {
                limit: this.limit,
            });
        },

        loadMore: async function () {
            this.limit += this.limit;
            await this.getPedidos();
        },
    },
};
</script>

<style>
.filter-btn-pedidos {
    margin-top: 4px;
    margin-left: 24px;
}

.filters {
    height: auto;
}
</style>
