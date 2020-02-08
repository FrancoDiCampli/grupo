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
                        <template slot="filter">
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-select
                                        v-model="sucursalID"
                                        :items="sucursales"
                                        item-text="nombre"
                                        item-value="id"
                                        label="Sucursal"
                                        outlined
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </template>
                        <template slot="loadMore">
                            <br />
                            <v-row justify="center" v-if="$store.state.ventas.ventas">
                                <v-btn
                                    :loading="$store.state.inProcess"
                                    :disabled="limit >= $store.state.ventas.ventas.total"
                                    @click="loadMore()"
                                    color="secondary"
                                    outlined
                                    tile
                                >Cargar Más</v-btn>
                            </v-row>
                        </template>
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
        limit: 10,
        sucursales: [],
        sucursalID: null
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
        this.getSucursales();
    },

    methods: {
        getVentas: async function() {
            await this.$store.dispatch("ventas/index", {
                negocio_id: this.sucursalID,
                limit: this.limit
            });
        },

        getFacturas: async function() {
            await this.$store.dispatch("facturas/index", {
                negocio_id: this.sucursalID,
                limit: this.limit
            });
        },

        getSucursales: async function() {
            await this.$store.dispatch("sucursales/index");
            this.$store.state.sucursales.sucursales.negocios.forEach(
                element => {
                    this.sucursales.push(element);
                }
            );
            this.sucursales.push({ id: null, nombre: "TODAS LAS SUCURSALES" });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getVentas();
            await this.getFacturas();
        }
    }
};
</script>

<style></style>
