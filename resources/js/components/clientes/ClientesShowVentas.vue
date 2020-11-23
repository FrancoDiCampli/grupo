<template>
    <div>
        <v-container>
            <v-expansion-panels>
                <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Remitos de ventas
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.facturas.length > 0">
                            <v-simple-table>
                                <thead>
                                    <tr>
                                        <th class="text-xs-left">Venta N°</th>
                                        <th class="text-xs-left">Fecha</th>
                                        <th class="text-xs-left">Importe</th>
                                        <th class="text-xs-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(remito, index) in $store.state.clientes
                                            .cliente.facturas"
                                        :key="index"
                                    >
                                        <td>{{ remito.numventa }}</td>
                                        <td>{{ remito.fecha }}</td>
                                        <td>{{ remito.total }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn color="secondary" text icon v-on="on">
                                                        <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="
                                                            `/remitos/show/${remito.id}`
                                                        "
                                                    >
                                                        <v-list-item-title>Detalles</v-list-item-title>
                                                    </v-list-item>
                                                    <v-list-item @click="printRemito(remito.id)">
                                                        <v-list-item-title>Imprimir</v-list-item-title>
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </div>
                        <div v-else>
                            <h2 class="text-center">No se han registrado ventas a nombre de este cliente</h2>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Facturas
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.invoices.length > 0">
                            <v-simple-table>
                                <thead>
                                    <tr>
                                        <th class="text-xs-left">Factura N°</th>
                                        <th class="text-xs-left">Fecha</th>
                                        <th class="text-xs-left">Importe</th>
                                        <th class="text-xs-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(factura, index) in $store.state.clientes
                                            .cliente.invoices"
                                        :key="index"
                                    >
                                        <td>{{ factura.comprobanteadherido }}</td>
                                        <td>{{ factura.fecha }}</td>
                                        <td>{{ factura.total }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn color="secondary" text icon v-on="on">
                                                        <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="
                                                            `/facturas/show/${factura.id}`
                                                        "
                                                    >
                                                        <v-list-item-title>Detalles</v-list-item-title>
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </div>
                        <div v-else>
                            <h2 class="text-center">No se han registrado ventas a nombre de este cliente</h2>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Entregas
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.facturas.length > 0">
                            <v-simple-table>
                                <thead>
                                    <tr>
                                        <th class="text-xs-left">Entrega N°</th>
                                        <th class="text-xs-left">Fecha</th>
                                        <th class="text-xs-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(entrega, index) in $store.state.clientes
                                            .cliente.entregas"
                                        :key="index"
                                    >
                                        <td>{{ entrega.numentrega }}</td>
                                        <td>{{ entrega.fecha }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn color="secondary" text icon v-on="on">
                                                        <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="
                                                            `/entregas/show/${entrega.id}`
                                                        "
                                                    >
                                                        <v-list-item-title>Detalles</v-list-item-title>
                                                    </v-list-item>
                                                    <v-list-item @click="printEntrega(entrega.id)">
                                                        <v-list-item-title>Imprimir</v-list-item-title>
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </div>
                        <div v-else>
                            <h2 class="text-center">No se han registrado ventas a nombre de este cliente</h2>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-container>
    </div>
</template>

<script>
export default {
    methods: {
        printRemito(id) {
            this.$store.dispatch("PDF/printVenta", { id: id });
        },

        printEntrega(id) {
            this.$store.dispatch("PDF/printEntrega", { id: id });
        },
    }
};
</script>

<style>

.no-shadow {
    border-radius: 0px !important;
    border-bottom: 1px solid #bbbbbb;
}

.no-shadow::before {
    box-shadow: none !important;
}

</style>
