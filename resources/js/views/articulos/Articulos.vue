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
                    to="/articulos/nuevo"
                    v-if="checkRole()"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Crear articulo</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ArticulosIndex :limit="limit">
                        <br />
                        <v-row
                            justify="center"
                            v-if="$store.state.articulos.articulos"
                        >
                            <v-btn
                                :disabled="
                                    limit >=
                                        $store.state.articulos.articulos.total
                                "
                                :loading="$store.state.inProcess"
                                @click="loadMore()"
                                color="primary"
                                outlined
                                tile
                                >Cargar Más</v-btn
                            >
                        </v-row>
                    </ArticulosIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ArticulosIndex from "../../components/articulos/ArticulosIndex";

export default {
    data: () => ({
        limit: 12
    }),

    components: {
        ArticulosIndex
    },

    mounted() {
        this.getArticulos();
    },

    methods: {
        getArticulos() {
            this.$store.dispatch("articulos/index", {
                limit: this.limit
            });
        },

        loadMore() {
            this.limit += this.limit;
            this.getArticulos();
        },

        checkRole() {
            if(this.$store.state.auth.user) {
                if(
                    this.$store.state.auth.user.rol =='superAdmin' || 
                    this.$store.state.auth.user.rol =='administrador'
                ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
};
</script>

<style></style>
