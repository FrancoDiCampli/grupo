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
                    to="/compras/nueva"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva compra</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ComprasIndex @erase="getCompras()">
                        <br />
                        <v-row
                            justify="center"
                            v-if="$store.state.compras.compras"
                        >
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="
                                    limit >= $store.state.compras.compras.total
                                "
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                                >Cargar Más</v-btn
                            >
                        </v-row>
                    </ComprasIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ComprasIndex from "../../components/compras/ComprasIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        ComprasIndex
    },

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getCompras();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },

    methods: {
        getCompras: async function() {
            await this.$store.dispatch("compras/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getCompras();
        },

        loadOnScroll() {
            if(window.scrollY >= (document.body.clientHeight - window.innerHeight)) {
                this.loadMore();
            }
        },
    }
};
</script>

<style></style>
