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
                    to="/proveedores/nuevo"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nuevo proveedor</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ProveedoresIndex :limit="limit">
                        <br />
                        <v-row justify="center" v-if="$store.state.proveedores.proveedores">
                            <v-btn
                                :disabled="limit >= $store.state.proveedores.proveedores.total"
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </v-row>
                    </ProveedoresIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ProveedoresIndex from "../../components/proveedores/ProveedoresIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        ProveedoresIndex
    },

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getProveedores();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },

    methods: {
        getProveedores() {
            this.$store.dispatch("proveedores/index", {
                limit: this.limit
            });
        },

        loadMore() {
            this.limit += this.limit;
            this.getProveedores();
        },

        loadOnScroll() {
            if(window.scrollY >= (document.body.clientHeight - window.innerHeight)) {
                this.loadMore();
            }
        },
    }
};
</script>

<style>
</style>