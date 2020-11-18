<template>
    <div>
        <v-container fluid grid-list-md>
            <v-row justify="space-around">
                <v-col cols="12" sm="5">
                    <v-card class="elevation-0">
                        <v-card-title>
                            <h5>Datos personales:</h5>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list dense>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>CUIT / CUIL:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.documentounico }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Apellido y nombre:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.razonsocial }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Condición frente al IVA:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.condicioniva }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Observaciones:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.observaciones }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
                <v-divider vertical></v-divider>
                <v-col cols="12" sm="5">
                    <v-card class="elevation-0">
                        <v-card-title>
                            <h5>Contacto:</h5>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list dense>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>TEL / CEL:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.telefono }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Email:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.email }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Provincia:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.provincia }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Localidad:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.localidad }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Codigo postal:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.codigopostal }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item style="margin: 10px 0px;">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <b>Dirección:</b>
                                    </v-list-item-title>
                                    <v-list-item-subtitle
                                        class="black--text"
                                    >{{ $store.state.clientes.cliente.cliente.direccion }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
                <v-col cols="12" v-if="$store.state.clientes.cliente.contactos.length > 0">
                    <v-divider></v-divider>
                    <v-card class="elevation-0">
                        <v-card-title>
                            <h5>Contactos:</h5>
                        </v-card-title>
                        <v-simple-table>
                            <thead>
                                <tr>
                                    <th class="text-xs-left">Nombre</th>
                                    <th class="text-xs-left">TEL / CEL</th>
                                    <th class="text-xs-left hidden-sm-and-down">Email</th>
                                    <th class="text-xs-left hidden-sm-and-down">Cargo</th>
                                    <th class="text-xs-left"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item,index) in $store.state.clientes.cliente.contactos"
                                    :key="index"
                                >
                                    <td>{{item.nombre}}</td>
                                    <td>{{item.numero}}</td>
                                    <td class="hidden-sm-and-down">{{item.email}}</td>
                                    <td class="hidden-sm-and-down">{{item.cargo}}</td>
                                </tr>
                            </tbody>
                        </v-simple-table>
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