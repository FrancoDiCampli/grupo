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
                    @click="$router.go(-1)"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card shaped outlined>
                    <v-card-title>Nuevo Articulo</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form ref="CreateArticulos" @submit.prevent="saveArticulo()">
                                    <ArticulosForm mode="create" ref="ArticuloForm"></ArticulosForm>
                                    <v-row justify="center">
                                        <v-btn
                                            type="submit"
                                            tile
                                            class="elevation-0"
                                            color="secondary"
                                            :disabled="$store.state.inProcess"
                                            :loading="$store.state.inProcess"
                                        >Guardar</v-btn>
                                    </v-row>
                                    <br />
                                </v-form>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ArticulosForm from "../../components/articulos/ArticulosForm";

export default {
    data: () => ({
        inProcess: false,
    }),

    components: {
        ArticulosForm,
    },

    mounted() {
        this.$refs.CreateArticulos.reset();
    },

    methods: {
        saveArticulo: async function () {
            if (this.$refs.CreateArticulos.validate()) {
                let categoriaForm = this.$store.state.articulos.form.categoria;
                let marcaForm = this.$store.state.articulos.form.marca;
                this.$store.state.articulos.form.foto = this.$refs.ArticuloForm.getFoto();

                if (typeof categoriaForm == "object") {
                    this.$store.state.articulos.form.categoria =
                        categoriaForm.categoria;
                }

                if (typeof marcaForm == "object") {
                    this.$store.state.articulos.form.marca = marcaForm.marca;
                }

                await this.$store.dispatch("articulos/save");
                this.$router.push("/articulos");
            }
        },
    },
};
</script>

<style></style>
