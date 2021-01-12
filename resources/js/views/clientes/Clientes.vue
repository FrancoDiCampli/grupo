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
                    to="/clientes/nuevo"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nuevo cliente</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ClientesIndex :limit="limit">
                        <template slot="cliente">
                            <br />
                            <v-row justify="center" v-if="$store.state.clientes.clientes">
                                <v-btn
                                    :loading="$store.state.inProcess"
                                    :disabled="
                                        limit >=
                                           $store.state.clientes.clientes.clientes.total
                                    "
                                    @click="loadMore()"
                                    color="secondary"
                                    outlined
                                    tile
                                >Cargar Más</v-btn>
                            </v-row>
                        </template>
                        <template slot="distribuidor">
                            <br />
                            <v-row justify="center" v-if="$store.state.clientes.clientes">
                                <v-btn
                                    :loading="$store.state.inProcess"
                                    :disabled="
                                        limit >=
                                            $store.state.clientes.clientes.distribuidores.total
                                    "
                                    @click="loadMore()"
                                    color="secondary"
                                    outlined
                                    tile
                                >Cargar Más</v-btn>
                            </v-row>
                        </template>
                    </ClientesIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ClientesIndex from "../../components/clientes/ClientesIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        ClientesIndex
    },

    watch: {
        sucursalID() {
            this.getClientes();
        }
    },

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getClientes();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },
    
    methods: {
        getClientes: async function() {
            await this.$store.dispatch("clientes/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getClientes();
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

<style>
.filter-btn-clientes {
    margin-top: 4px;
    margin-left: 24px;
}

.filters {
    height: auto;
}
</style>
