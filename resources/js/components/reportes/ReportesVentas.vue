<template>
    <div>
        <v-row justify="center">
            <v-col cols="5">
                <v-dialog
                    ref="ventasDesde"
                    v-model="modalVentasDesde"
                    :return-value.sync="fechaVentasDesde"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="fechaVentasDesde"
                            label="Fecha Desde"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="fechaVentasDesde"
                        @change="getVentas()"
                        scrollable
                        locale="es"
                        format="YYYYMMDD"
                        color="primary"
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="modalVentasDesde = false">Cancel</v-btn>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.ventasDesde.save(fechaVentasDesde)"
                        >OK</v-btn>
                    </v-date-picker>
                </v-dialog>
            </v-col>

            <v-col cols="5">
                <v-dialog
                    ref="ventasHasta"
                    v-model="modalVentasHasta"
                    :return-value.sync="fechaVentasHasta"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="fechaVentasHasta"
                            label="Fecha Hasta"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="fechaVentasHasta"
                        @change="getVentas()"
                        scrollable
                        locale="es"
                        format="YYYYMMDD"
                        color="primary"
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="modalVentasHasta = false">Cancel</v-btn>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.ventasHasta.save(fechaVentasHasta)"
                        >OK</v-btn>
                    </v-date-picker>
                </v-dialog>
            </v-col>
        </v-row>

        <v-tabs vertical :grow="true">
            <v-tab>Ventas Por Fechas</v-tab>
            <v-tab>Ventas Por Vendedores</v-tab>
            <v-tab>Ventas Por Clientes</v-tab>
            <v-tab>Ventas Por Condiciones</v-tab>
            <v-tab>Ventas Por Sucursales</v-tab>

            <v-tab-item>
                <v-card text>
                    <ve-line
                        v-if="$store.state.reportes.ventas"
                        :legend-visible="false"
                        :data="$store.state.reportes.ventas.ventas.ventasFechaChart"
                    ></ve-line>

                    <!-- <div>
                        <v-container>
                            <v-row justify="center">
                                <v-col cols="12" sm="10" lg="8">
                                    <v-card>
                                        <v-card-text>
                                            <template>
                                                <v-simple-table>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-xs-left">Nº Venta</th>
                                                            <th class="text-xs-left">Importe</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-if="$store.state.reportes.ventas">
                                                        <tr
                                                            v-for="(factura,index) in $store.state.reportes.ventas.ventas.ventasFecha"
                                                            :key="index"
                                                        >
                                                            <td>{{ factura.numventa }}</td>
                                                            <td>{{ factura.total }}</td>
                                                        </tr>
                                                    </tbody>
                                                </v-simple-table>
                                            </template>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-container>
                    </div>-->
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card text>
                    <ve-pie
                        v-if="$store.state.reportes.ventas"
                        :legend-visible="false"
                        :data="$store.state.reportes.ventas.ventas.ventasVendedores"
                    ></ve-pie>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <ve-pie
                    v-if="$store.state.reportes.ventas"
                    :legend-visible="false"
                    :data="$store.state.reportes.ventas.ventas.ventasClientes"
                ></ve-pie>
            </v-tab-item>
            <v-tab-item>
                <v-card text>
                    <ve-pie
                        v-if="$store.state.reportes.ventas"
                        :legend-visible="false"
                        :data="$store.state.reportes.ventas.ventas.ventasCondiciones"
                    ></ve-pie>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card text>
                    <ve-pie
                        v-if="$store.state.reportes.ventas"
                        :legend-visible="false"
                        :data="$store.state.reportes.ventas.ventas.ventasSucursales"
                    ></ve-pie>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
export default {
    data() {
        return {
            modalVentasDesde: false,
            modalVentasHasta: false,
            fechaVentasDesde: null,
            fechaVentasHasta: null
        };
    },

    mounted() {
        this.getVentas();
    },

    methods: {
        getVentas: async function() {
            let response = await this.$store.dispatch("reportes/ventas", {
                limit: 10,
                desde: this.fechaVentasDesde,
                hasta: this.fechaVentasHasta
            });
        }
    }
};
</script>

<style>
</style>