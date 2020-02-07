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
                    to="/sucursales/nueva"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva sucursal</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <SucursalesIndex>
                        <br />
                        <v-row justify="center" v-if="$store.state.sucursales.sucursales">
                            <v-btn
                                :disabled="limit >= $store.state.sucursales.sucursales.total"
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </v-row>
                    </SucursalesIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import SucursalesIndex from "../../components/sucursales/SucursalesIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        SucursalesIndex
    },

    mounted() {
        this.getSucursales();
    },

    methods: {
        getSucursales() {
            this.$store.dispatch("sucursales/index", {
                limit: this.limit
            });
        },

        loadMore() {
            this.limit += this.limit;
            this.getNegocios();
        }
    }
};
</script>

<style>
</style>