<template>
    <div>
        <v-container fluid grid-list-md>
            <v-row justify="space-around">
                <v-col cols="12" sm="5">
                    <v-card class="elevation-0">
                        <v-card-title>
                            <h5>Datos generales:</h5>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list dense>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Articulo:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.articulos.articulo.articulo.articulo }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Descripción:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.articulos.articulo.articulo.descripcion }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Codigo:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.articulos.articulo.articulo.codarticulo }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Categoria:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.articulos.articulo.categoria }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Marca:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.articulos.articulo.marca }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
                <v-divider vertical></v-divider>
                <v-col cols="12" sm="5">
                    <v-card class="elevation-0">
                        <v-card-title>
                            <h5>Datos financieros:</h5>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list dense>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Precio:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >$ {{ $store.state.articulos.articulo.articulo.precio }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Presentación:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.articulos.articulo.articulo.litros }} Litros</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {};
</script>

<style>
</style>