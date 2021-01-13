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

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ConsignacionesIndex>
                        <br />
                        <v-row justify="center" v-if="$store.state.compras.compras">
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="
                                    limit >= $store.state.compras.compras.total
                                "
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </v-row>
                    </ConsignacionesIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ConsignacionesIndex from "../../components/consignaciones/ConsignacionesIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        ConsignacionesIndex
    },

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getConsignaciones();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },

    methods: {
        getConsignaciones: async function() {
            await this.$store.dispatch("consignaciones/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getConsignaciones();
        },

        loadOnScroll() {
            if(document.body.scrollTop + document.body.clientHeight >= document.body.scrollHeight) {
                if(!this.$store.state.inProcess) {
                    this.loadMore();
                }
            }
        },
    }
};
</script>

<style></style>