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
                    <PresupuestosIndex>
                        <v-row justify="center" v-if="$store.state.clientes.clientes">
                            <br />
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
                            >Cargar Más</v-btn>
                        </v-row>
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
    },

    methods: {
        getPresupuestos: async function() {
            await this.$store.dispatch("presupuestos/index", {
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

<style>
.filter-btn-presupuestos {
    margin-top: 4px;
    margin-left: 24px;
}

.filters {
    height: auto;
}
</style>
