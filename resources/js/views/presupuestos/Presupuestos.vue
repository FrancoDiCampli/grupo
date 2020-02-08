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
                    to="/presupuestos/nuevo"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nuevo presupuesto</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-row>
                        <v-btn
                            color="primary"
                            class="filter-btn-presupuestos"
                            icon
                            @click="filterMenu = !filterMenu"
                        >
                            <v-icon size="medium">fas fa-filter</v-icon>
                        </v-btn>
                    </v-row>
                    <v-expand-transition>
                        <div class="filters" v-if="filterMenu">
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
                        </div>
                    </v-expand-transition>
                    <PresupuestosIndex>
                        <template slot="loadMore">
                            <br />
                            <v-row
                                justify="center"
                                v-if="$store.state.clientes.clientes"
                            >
                                <v-btn
                                    :loading="$store.state.inProcess"
                                    :disabled="
                                        limit >=
                                            $store.state.clientes.clientes.total
                                    "
                                    @click="loadMore()"
                                    color="secondary"
                                    outlined
                                    tile
                                    >Cargar Más</v-btn
                                >
                            </v-row>
                        </template>
                    </PresupuestosIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import PresupuestosIndex from "../../components/presupuestos/PresupuestosIndex";

export default {
    data: () => ({
        limit: 10,
        sucursales: [],
        sucursalID: null,
        filterMenu: false
    }),

    components: {
        PresupuestosIndex
    },

    watch: {
        sucursalID() {
            this.getPresupuestos();
        }
    },

    mounted() {
        this.getPresupuestos();
        this.getSucursales();
    },

    methods: {
        getPresupuestos: async function() {
            await this.$store.dispatch("presupuestos/index", {
                limit: this.limit,
                negocio_id: this.sucursalID
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
            await this.getPresupuestos();
        }
    }
};
</script>

<style>
.filter-btn-presupuestos {
    margin-top: 4px;
    margin-left: 24px;
}

.filters {
    height: auto;
}
</style>
