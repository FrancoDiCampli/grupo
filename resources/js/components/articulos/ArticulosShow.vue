<template>
    <div>
        <v-row justify="center">
            <v-col cols="12">
                <v-card
                    v-if="$store.state.articulos.articulo"
                    shaped
                    outlined
                    :loading="$store.state.inProcess"
                >
                    <div v-if="mode == 'edit'">
                        <v-card-title>Editar Articulo</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-form ref="articulosEditForm" @submit.prevent="updateArticulo()">
                                <ArticulosForm mode="edit" ref="articulosForm"></ArticulosForm>
                                <v-row justify="center">
                                    <v-btn
                                        tile
                                        @click="mode = 'show'"
                                        :disabled="$store.state.inProcess"
                                        outlined
                                        color="secondary"
                                        class="mx-2"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        tile
                                        class="mx-2 elevation-0"
                                        :loading="$store.state.inProcess"
                                        :disabled="$store.state.inProcess"
                                        type="submit"
                                        color="secondary"
                                    >Editar</v-btn>
                                </v-row>
                                <br />
                            </v-form>
                        </v-card-text>
                    </div>
                    <div v-else>
                        <div>
                            <br />
                            <div
                                v-if="$store.state.auth.user.rol == 'superAdmin' || $store.state.auth.user.rol == 'administrador'"
                            >
                                <v-menu>
                                    <template v-slot:activator="{ on }">
                                        <v-btn
                                            absolute
                                            right
                                            text
                                            icon
                                            dark
                                            color="secondary"
                                            v-on="on"
                                        >
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item @click="editArticulo()">
                                            <v-list-item-title>Editar</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="mode = 'delete'">
                                            <v-list-item-title>Eliminar</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </div>
                            <v-col cols="12">
                                <v-layout justify-center>
                                    <v-avatar size="200">
                                        <img :src="$store.state.articulos.articulo.articulo.foto" />
                                    </v-avatar>
                                </v-layout>
                            </v-col>

                            <v-col cols="12">
                                <br />
                                <h1
                                    class="text-center secondary--text"
                                >{{ $store.state.articulos.articulo.articulo.articulo }}</h1>
                                <h3
                                    class="text-center secondary--text"
                                >{{ $store.state.articulos.articulo.articulo.descripcion }}</h3>
                            </v-col>
                        </div>
                        <br />
                        <div v-if="mode == 'show'">
                            <template>
                                <div>
                                    <v-tabs
                                        grow
                                        slider-color="secondary"
                                        active-class="secondary--text"
                                    >
                                        <v-tab>Datos</v-tab>
                                        <v-tab>Inventarios</v-tab>
                                        <v-tab-item style="background: white !important;">
                                            <div v-if="$store.state.articulos.articulo">
                                                <ArticulosShowData></ArticulosShowData>
                                            </div>
                                        </v-tab-item>
                                        <v-tab-item style="background: white !important;">
                                            <ArticulosShowInventarios></ArticulosShowInventarios>
                                        </v-tab-item>
                                    </v-tabs>
                                </div>
                                <br />
                            </template>
                        </div>
                        <div v-else-if="mode == 'delete'">
                            <div class="articulos-delete">
                                <h2 class="text-center white--text">¿Estas Seguro?</h2>
                                <br />
                                <v-divider dark></v-divider>
                                <br />
                                <p
                                    class="text-center white--text"
                                >¿Realmente deseas eliminar este Producto?</p>
                                <p class="text-center white--text">Este Cambio es Irreversible</p>
                                <br />
                                <v-row justify="center">
                                    <v-btn
                                        @click="mode = 'show'"
                                        tile
                                        :disabled="$store.state.inProcess"
                                        class="mx-2 red--text elevation-0"
                                        color="white"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        tile
                                        @click="deleteArticulo()"
                                        outlined
                                        :loading="$store.state.inProcess"
                                        :disabled="$store.state.inProcess"
                                        color="white"
                                        class="mx-2"
                                    >Eliminar</v-btn>
                                </v-row>
                                <br />
                            </div>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ArticulosForm from "./ArticulosForm";
import ArticulosShowData from "./ArticulosShowData";
import ArticulosShowInventarios from "./ArticulosShowInventarios";

export default {
    name: "ArticulosShow",

    data() {
        return {
            mode: "show"
        };
    },

    props: ["id"],

    components: {
        ArticulosForm,
        ArticulosShowData,
        ArticulosShowInventarios
    },

    mounted() {
        this.getArticulo();
    },

    methods: {
        getArticulo() {
            this.$store.dispatch("articulos/show", {
                id: this.id
            });
        },

        editArticulo: async function() {
            await this.$store.dispatch("articulos/edit", {
                data: this.$store.state.articulos.articulo.articulo
            });

            this.mode = "edit";
        },

        updateArticulo: async function() {
            if (this.$refs.articulosEditForm.validate()) {
                let id = this.$store.state.articulos.form.id;
                this.$store.state.articulos.form.foto = this.$refs.articulosForm.getFoto();
                await this.$store.dispatch("articulos/update", {
                    id: id
                });
                await this.$store.dispatch("articulos/show", {
                    id: id
                });
                this.mode = "show";
            }
        },

        deleteArticulo: async function() {
            await this.$store.dispatch("articulos/destroy", {
                id: this.$store.state.articulos.articulo.articulo.id
            });
            this.mode = "show";
            this.$router.push("/articulos");
        }
    }
};
</script>

<style lang="scss">
.articulos-delete {
    padding-top: 32px;
    border-bottom-right-radius: 24px;
    background-color: #f44336;
}
</style>