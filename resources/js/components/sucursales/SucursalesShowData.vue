<template>
    <div>
        <v-container fluid grid-list-md>
            <v-row justify="space-around">
                <v-col cols="12">
                    <v-card class="elevation-0">
                        <v-card-title>
                            <h5>Datos de la sucursal:</h5>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list dense>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Nombre:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.sucursales.sucursal.negocio.nombre }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Dirección:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.sucursales.sucursal.negocio.direccion }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Localidad:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.sucursales.sucursal.negocio.localidad }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Provincia:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.sucursales.sucursal.negocio.provincia }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Codigo Postal:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.sucursales.sucursal.negocio.codigopostal }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Observaciones:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.sucursales.sucursal.negocio.observaciones }}</v-list-item-subtitle>
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