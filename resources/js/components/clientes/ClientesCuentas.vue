<template>
    <div>
        <div v-if="$store.state.clientes.cliente.cuentas.length > 0">
            <v-data-table
                :headers="$vuetify.breakpoint.xsOnly ? headersMobile : headers"
                :items="$store.state.clientes.cliente.cuentas"
                item-key="id"
                :expanded.sync="expand"
                show-expand
                hide-default-footer
                :items-per-page="-1"
                :mobile-breakpoint="0"
            >
                <template v-slot:expanded-item="{ headers, item }">
                    <td :colspan="headers.length" class="py-5">
                        <v-card class="elevation-0" outlined>
                            <v-card-title class="py-2"
                                >Movimientos:</v-card-title
                            >
                            <v-divider></v-divider>
                            <v-card-text class="ma-0 pa-0">
                                <v-simple-table>
                                    <thead>
                                        <tr>
                                            <th class="text-xs-left">
                                                Importe
                                            </th>
                                            <th class="txt-xs-left">Fecha</th>
                                            <th class="text-xs-left">Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(movimiento,
                                            index) in item.movimientos"
                                            :key="index"
                                        >
                                            <td>{{ movimiento.importe | formatCurrency('USD') }}</td>
                                            <td>{{ movimiento.fecha | formatDate }}</td>
                                            <td>{{ movimiento.tipo }}</td>
                                        </tr>
                                    </tbody>
                                </v-simple-table>
                            </v-card-text>
                        </v-card>
                        <br />
                    </td>
                </template>
            </v-data-table>
        </div>
        <div v-else>
            <br />
            <h2 class="text-center">El cliente no posee cuentas corrientes</h2>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        expand: [],
        headers: [
            { text: "Venta N°", value: "factura.numventa", sortable: false },
            { text: "Importe", value: "importe", sortable: false },
            { text: "Saldo", value: "saldo", sortable: false },
            { text: "Alta", value: "alta", sortable: false },
            { text: "Ultimo pago", value: "ultimopago", sortable: false },
            { text: "Estado", value: "estado", sortable: false }
        ],
        headersMobile: [
            { text: "Venta N°", value: "factura.numventa", sortable: false },
            { text: "Saldo", value: "saldo", sortable: false },
            { text: "Alta", value: "alta", sortable: false },
            { text: "Estado", value: "estado", sortable: false }
        ]
    })
};
</script>

<style></style>
