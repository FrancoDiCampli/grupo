<template>
    <div>
        <v-row justify="center">
            <v-col cols="5">
                <v-dialog
                    ref="DetallesDesde"
                    v-model="modalDetallesDesde"
                    :return-value.sync="fechaDetallesDesde"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="fechaDetallesDesde"
                            label="Fecha Desde"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="fechaDetallesDesde"
                        @change="getDetallesCompras(); getDetallesVentas()"
                        scrollable
                        locale="es"
                        format="YYYYMMDD"
                        color="primary"
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="modalDetallesDesde = false">Cancel</v-btn>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.DetallesDesde.save(fechaDetallesDesde)"
                        >OK</v-btn>
                    </v-date-picker>
                </v-dialog>
            </v-col>

            <v-col cols="5">
                <v-dialog
                    ref="DetallesHasta"
                    v-model="modalDetallesHasta"
                    :return-value.sync="fechaDetallesHasta"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="fechaDetallesHasta"
                            label="Fecha Hasta"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="fechaDetallesHasta"
                        @change="getDetallesCompras(); getDetallesVentas()"
                        scrollable
                        locale="es"
                        format="YYYYMMDD"
                        color="primary"
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="modalDetallesHasta = false">Cancel</v-btn>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.DetallesHasta.save(fechaDetallesHasta)"
                        >OK</v-btn>
                    </v-date-picker>
                </v-dialog>
            </v-col>
        </v-row>

        <v-tabs vertical :grow="true">
            <v-tab>Ventas Por Producto</v-tab>
            <v-tab>Compras Por Producto</v-tab>

            <v-tab-item>
                <ve-line
                    v-if="$store.state.reportes.productosVentas"
                    :legend-visible="false"
                    :data="$store.state.reportes.productosVentas.detalles.ventasProductosChart"
                ></ve-line>

                <!-- <v-layout justify-center>
                    <v-simple-table v-if="$store.state.reportes.productosVentas">
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left">Cod. Articulo</th>
                                    <th class="text-left">Articulo</th>
                                    <th class="text-left">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in $store.state.reportes.productosVentas.detalles.ventasProductos"
                                    :key="index"
                                >
                                    <td>{{ item.codarticulo }}</td>
                                    <td>{{ item.articulo }}</td>
                                    <td>{{ item.cantidad }}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-layout>-->
            </v-tab-item>

            <v-tab-item>
                <ve-line
                    v-if="$store.state.reportes.productosCompras"
                    :legend-visible="false"
                    :data="$store.state.reportes.productosCompras.detalles.comprasProductosChart"
                ></ve-line>

                <!-- <v-layout justify-center>
                    <v-simple-table v-if="$store.state.reportes.productosCompras">
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left">Cod. Articulo</th>
                                    <th class="text-left">Articulo</th>
                                    <th class="text-left">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in $store.state.reportes.productosCompras.detalles.comprasProductos"
                                    :key="index"
                                >
                                    <td>{{ item.codarticulo }}</td>
                                    <td>{{ item.articulo }}</td>
                                    <td>{{ item.cantidad }}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-layout>-->
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
export default {
    data() {
        return {
            modalDetallesDesde: false,
            modalDetallesHasta: false,
            fechaDetallesDesde: null,
            fechaDetallesHasta: null
        };
    },

    mounted() {
        this.getDetallesCompras();
        this.getDetallesVentas();
    },

    methods: {
        getDetallesCompras: async function() {
            let response = await this.$store.dispatch(
                "reportes/detallesCompras",
                {
                    limit: 10,
                    desde: this.fechaDetallesDesde,
                    hasta: this.fechaDetallesHasta
                }
            );
        },
        getDetallesVentas: async function() {
            let response = await this.$store.dispatch(
                "reportes/detallesVentas",
                {
                    limit: 10,
                    desde: this.fechaDetallesDesde,
                    hasta: this.fechaDetallesHasta
                }
            );
        }
    }
};
</script>

<style>
</style>