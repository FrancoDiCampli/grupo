<template>
    <div>
        <v-container>
            <v-expansion-panels>
                <!-- <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Notas de pedido
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>

                    </v-expansion-panel-content>
                </v-expansion-panel> -->
                <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Remitos de ventas
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.ventas.length > 0">
                            <!-- <v-simple-table>
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
                                            .cliente.ventas"
                                        :key="index"
                                    >
                                        <td>{{ remito.numventa }}</td>
                                        <td>{{ remito.fecha }}</td>
                                        <td>{{ remito.total | formatCurrency('USD') }}</td>
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
                            </v-simple-table> -->
                            <v-data-table
                                :headers="remitoHeaders"
                                :items="$store.state.clientes.cliente.ventas"
                                hide-default-footer
                                :items-per-page="-1"
                                :mobile-breakpoint="0"
                                :single-select="false"
                            >
                                <template v-slot:item="{ item }">
                                    <tr>
                                        <td>
                                            <v-checkbox
                                                v-model="remitoSelected"
                                                :value="item.id"
                                                :disabled="item.todofacturado"
                                                v-if="checkRole()"
                                            ></v-checkbox>
                                        </td>
                                        <td class="hidden-xs-only">
                                            {{ item.comprobanteadherido || item.numventa }}
                                        </td>
                                        <td>{{ item.total | formatCurrency('USD') }}</td>
                                        <td class="hidden-sm-and-down">{{ item.fecha }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn
                                                        color="secondary"
                                                        text
                                                        icon
                                                        v-on="on"
                                                    >
                                                        <v-icon size="medium">
                                                            fas fa-ellipsis-v
                                                        </v-icon>
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="`/remitos/show/${item.id}`"
                                                    >
                                                        <v-list-item-title
                                                            >Detalles</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item @click="printRemito(item.id)">
                                                        <v-list-item-title
                                                            >Imprimir</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="checkDelivery(item)"
                                                        @click="delivery(item)"
                                                    >
                                                        <v-list-item-title
                                                            >Generar
                                                            entrega</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="remitosCheckDelete(item)"
                                                        @click="openDeleteDialog(item.id)"
                                                    >
                                                        <v-list-item-title
                                                            >Eliminar</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </td>
                                    </tr>
                                </template>
                            </v-data-table>
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
                        <div v-if="$store.state.clientes.cliente.facturas.length > 0">
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
                                            .cliente.facturas"
                                        :key="index"
                                    >
                                        <td>{{ factura.comprobanteadherido }}</td>
                                        <td>{{ factura.fecha | formatDate }}</td>
                                        <td>{{ factura.total | formatCurrency('USD') }}</td>
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
                            <h2 class="text-center">No se han registrado facturas a nombre de este cliente</h2>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Entregas
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.entregas.length > 0">
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
                                        <td>{{ entrega.fecha | formatDate }}</td>
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
                            <h2 class="text-center">No se han registrado entregas a nombre de este cliente</h2>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-container>

        <!-- DELETE REMITOS -->
        <v-dialog v-model="remitoDeleteDialog" persistent width="450px">
            <v-card v-if="inProcess">
                <v-row justify="center">
                    <v-progress-circular
                        :size="70"
                        :width="7"
                        color="primary"
                        indeterminate
                        style="margin: 32px 0 32px 0;"
                    ></v-progress-circular>
                </v-row>
            </v-card>
            <v-card v-else>
                <v-card-title class="headline">¿Estas seguro?</v-card-title>
                <v-card-text>
                    Se eliminará el remito seleccionado, esta acción es irreversible.
                    La cuenta corriente del cliente sera restaurada.
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="cancelDeleteRemito()"
                        :disabled="inProcess"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="success"
                        text
                        @click="deleteRemito()"
                        :disabled="inProcess"
                        >Aceptar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
export default {

    data: () => ({
        // GENERAL
        inProcess: false,
        // REMITOS
        remitoDeleteDialog: false,
        remitoDeleteId: null,
        remitoHeaders: [
            { text: "", sortable: false },
            { text: "N°", sortable: false, class: "hidden-xs-only" },
            { text: "Importe", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-sm-and-down" },
            { text: "", sortable: false }
        ],
        remitoSelected: []
    }),

    methods: {
        // REMITOS
        printRemito(id) {
            this.$store.dispatch("PDF/printVenta", { id: id });
        },
        
        checkDelivery(item) {
            let details = [];
            for (let i = 0; i < item.articulos.length; i++) {
                let disponible = item.articulos[i].pivot.disponible;
                let cantidad = item.articulos[i].pivot.cantidad;
                let cantidadentregado = item.articulos[i].pivot.cantidadentregado;
                if(disponible >= (cantidad - cantidadentregado)) {
                    details.push(item.articulos[i].pivot);
                }
            }

            if(details.length > 0) {
                return !item.todoentregado;
            } else {
                return false;
            }

        },

        delivery(item) {
            let details = [];
            for (let i = 0; i < item.articulos.length; i++) {
                item.articulos[i].pivot.litros = item.articulos[i].litros;
                details.push(item.articulos[i].pivot);
            }

            this.$store
                .dispatch("entregas/entregar", {
                    detalles: details,
                    cliente: item.cliente.razonsocial,
                    cliente_id: item.cliente.id,
                    venta_id: item.id,
                    fecha: moment(item.fecha, 'YYYY-MM-DD').format('YYYY-MM-DD'),
                    comprobanteadherido: item.comprobanteadherido
                })
                .then(() => {
                    this.$router.push("/entregas/create");
                });
        },

        remitosCheckDelete(item) {
            if(!item.hasFacturas && !item.hasEntregas && !item.hasPagos) {
                return true;
            } else {
                return false;
            }
        },

        remitosOpenDeleteDialog(id) {
            this.remitoDeleteId = id;
            this.remitoDeleteDialog = true;
        },

        cancelDelete() {
            this.remitoDeleteId = null;
            this.remitoDeleteDialog = false;
        },

        async deleteRemito() {
            this.inProcess = true;
            await this.$store.dispatch("remitos/destroy", { id: this.deleteId });
            await this.$store.dispatch("clientes/show", {
                id: this.id
            });
            this.inProcess = false;
            this.remitoDeleteDialog = false;
        },

        // ENTREGAS
        printEntrega(id) {
            this.$store.dispatch("PDF/printEntrega", { id: id });
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

<style>

.no-shadow {
    border-radius: 0px !important;
    border-bottom: 1px solid #bbbbbb;
}

.no-shadow::before {
    box-shadow: none !important;
}

</style>