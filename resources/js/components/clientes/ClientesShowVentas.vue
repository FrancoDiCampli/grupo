<template>
    <div>
        <v-container>
            <v-expansion-panels >
                <v-expansion-panel class="no-shadow" v-if="checkRole(['superAdmin', 'administrador', 'vendedor'])">
                    <v-expansion-panel-header>
                        Notas de pedido
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.pedidos.length > 0">
                            <v-data-table
                                :headers="pedidosHeaders"
                                :items="$store.state.clientes.cliente.pedidos"
                                hide-default-footer
                                :items-per-page="limit"
                                :mobile-breakpoint="0"
                            >
                                <template v-slot:item="{ item }">
                                    <tr>
                                        <td>
                                            {{
                                                item.comprobanteadherido ||
                                                    item.numpresupuesto
                                            }}
                                            
                                        </td>
                                        <td class="hidden-xs-only">{{ item.fecha }}</td>
                                        <td>{{ item.total | formatCurrency('USD') }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn
                                                        color="secondary"
                                                        text
                                                        icon
                                                        v-on="on"
                                                    >
                                                        <v-icon size="medium"
                                                            >fas fa-ellipsis-v</v-icon
                                                        >
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="`/pedidos/show/${item.id}`"
                                                    >
                                                        <v-list-item-title
                                                            >Detalles</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item @click="printPedido(item.id)">
                                                        <v-list-item-title
                                                            >Imprimir</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="item.numventa == null && checkRole(['superAdmin', 'administrador', 'vendedor'])"
                                                        :to="`/pedidos/editar/${item.id}`"
                                                    >
                                                        <v-list-item-title
                                                            >Editar</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="item.numventa == null && checkRole(['superAdmin', 'administrador', 'vendedor'])"
                                                        @click="preventSold(item.id)"
                                                    >
                                                        <v-list-item-title>
                                                            Generar Venta
                                                        </v-list-item-title>
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="item.numventa == null && checkRole(['superAdmin', 'administrador'])"
                                                        @click="pedidosOpenDeleteDialog(item.id)"
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
                        
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel class="no-shadow">
                    <v-expansion-panel-header>
                        Remitos de ventas
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="$store.state.clientes.cliente.ventas.length > 0">
                            <v-tooltip bottom v-if="remitoSelected.length > 0">
                                <template v-slot:activator="{ on }">
                                    <v-btn
                                        color="secondary"
                                        dark
                                        fab
                                        absolute
                                        right
                                        large
                                        v-on="on"
                                        @click="facturar"
                                        class="elevation-0"
                                    >
                                        <v-icon>fas fa-file-invoice-dollar</v-icon>
                                    </v-btn>
                                </template>
                                <span>Generar factura</span>
                            </v-tooltip>
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
                                                v-if="checkRole(['superAdmin', 'administrador'])"
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
                                                        v-if="checkDelivery(item) && checkRole(['superAdmin', 'administrador', 'vendedor'])"
                                                        @click="delivery(item)"
                                                    >
                                                        <v-list-item-title
                                                            >Generar
                                                            entrega</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="remitosCheckDelete(item) && checkRole(['superAdmin', 'administrador'])"
                                                        @click="remitosOpenDeleteDialog(item.id)"
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
                            <v-data-table
                                :headers="facturasHeaders"
                                :items="$store.state.clientes.cliente.facturas"
                                hide-default-footer
                                :items-per-page="-1"
                                :mobile-breakpoint="0"
                            >
                                <template v-slot:item="{ item }">
                                    <tr>
                                        <td>
                                            {{
                                                item.comprobanteadherido || item.numfactura
                                            }}
                                        </td>
                                        <td class="hidden-xs-only">{{ item.fecha }}</td>
                                        <td>{{ item.total | formatCurrency('USD') }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn
                                                        color="secondary"
                                                        text
                                                        icon
                                                        v-on="on"
                                                    >
                                                        <v-icon size="medium"
                                                            >fas fa-ellipsis-v</v-icon
                                                        >
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="`/facturas/show/${item.id}`"
                                                    >
                                                        <v-list-item-title
                                                            >Detalles</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="!item.hasPagos && checkRole(['superAdmin', 'administrador'])"
                                                        @click="facturasOpenDeleteDialog(item.id)"
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
                            <v-data-table
                                :headers="entregasHeaders"
                                :items="$store.state.clientes.cliente.entregas"
                                hide-default-footer
                                :items-per-page="-1"
                                :mobile-breakpoint="0"
                            >
                                <template v-slot:item="{ item }">
                                    <tr>
                                        <td>
                                            {{
                                                item.comprobanteadherido || item.numentrega
                                            }}
                                        </td>
                                        <td>{{ item.fecha }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn
                                                        color="secondary"
                                                        text
                                                        icon
                                                        v-on="on"
                                                    >
                                                        <v-icon size="medium"
                                                            >fas fa-ellipsis-v</v-icon
                                                        >
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="`/entregas/show/${item.id}`"
                                                    >
                                                        <v-list-item-title
                                                            >Detalles</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item @click="printEntrega(item.id)">
                                                        <v-list-item-title
                                                            >Imprimir</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="checkRole(['superAdmin', 'administrador'])"
                                                        @click="entregasOpenDeleteDialog(item.id)"
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
                            <h2 class="text-center">No se han registrado entregas a nombre de este cliente</h2>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-container>

        <!-- VENDER -->
        <v-dialog v-model="preventDialog" persistent width="450px">
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
                    <v-row>
                        <v-col cols="12">
                            Se generará un remito a partir de la nota de pedido
                            seleccionada
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                outlined
                                label="Remito adherido N°"
                                v-model="remitoadherido"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="cancelSold()"
                        :disabled="inProcess"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="success"
                        text
                        @click="sold()"
                        :disabled="inProcess"
                        >Aceptar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- DELETE PEDIDOS -->
        <v-dialog v-model="pedidosDeleteDialog" persistent width="450px">
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
                    Se eliminará el pedido seleccionado, esta acción es irreversible.
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="cancelDeletePedidos()"
                        :disabled="inProcess"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="success"
                        text
                        @click="deletePedido()"
                        :disabled="inProcess"
                        >Aceptar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

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

        <!-- DELETE FACTURAS -->
        <v-dialog v-model="facturasDeleteDialog" persistent width="450px">
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
                    Se eliminará la factura seleccionada, esta acción es irreversible.
                    El IVA asignado sera restaurado.
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="cancelDeleteFacturas()"
                        :disabled="inProcess"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="success"
                        text
                        @click="deleteFactura()"
                        :disabled="inProcess"
                        >Aceptar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- DELETE ENTREGAS -->
        <v-dialog v-model="entregasDeleteDialog" persistent width="450px">
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
                    Se eliminará la entrega seleccionada, esta acción es
                    irreversible. El stock sera restaurado.
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="cancelDeleteEntregas()"
                        :disabled="inProcess"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="success"
                        text
                        @click="deleteEntrega()"
                        :disabled="inProcess"
                        >Aceptar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from 'moment';

export default {

    data: () => ({
        // GENERAL
        inProcess: false,
        // PEDIDOS
        ventaID: null,
        preventDialog: false,
        remitoadherido: null,
        pedidosDeleteDialog: false,
        pedidosDeleteId: null,
        pedidosHeaders: [
            { text: "Número", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-xs-only" },
            { text: "Importe", sortable: false },
            { text: "", sortable: false }
        ],
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
        remitoSelected: [],
        // FACTURAS
        facturasDeleteDialog: false,
        facturasDeleteId: null,
        facturasHeaders: [
            { text: "N°", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-xs-only" },
            { text: "Importe", sortable: false },
            { text: "", sortable: false }
        ],
        // ENTREGAS
        entregasDeleteDialog: false,
        entregasDeleteId: null,
        entregasHeaders: [
            { text: "N°", sortable: false },
            { text: "Fecha", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    methods: {
        // PEDIDOS
        printPedido(id) {
            this.$store.dispatch("PDF/printPedido", { id: id });
        },

        preventSold(id) {
            this.ventaID = id;
            this.preventDialog = true;
        },

        async sold() {
            this.inProcess = true;
            await this.$store.dispatch("pedidos/vender", {
                id: this.ventaID,
                remitoadherido: this.remitoadherido
            });
            await this.$store.dispatch("clientes/show", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            this.preventDialog = false;
            this.inProcess = false;
            this.ventaID = null;
            this.remitoadherido = null;
        },

        cancelSold() {
            this.preventDialog = false;
            this.ventaID = null;
            this.remitoadherido = null;
        },

        pedidosOpenDeleteDialog(id) {
            this.pedidosDeleteId = id;
            this.pedidosDeleteDialog = true;
        },

        cancelDeletePedidos() {
            this.pedidosDeleteId = null;
            this.pedidosDeleteDialog = false;
        },

        async deletePedido() {
            this.inProcess = true;
            await this.$store.dispatch("pedidos/destroy", { id: this.pedidosDeleteId });
            await this.$store.dispatch("clientes/show", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            this.inProcess = false;
            this.pedidosDeleteDialog = false;
        },

        // REMITOS
        printRemito(id) {
            this.$store.dispatch("PDF/printVenta", { id: id });
        },
        
        facturar() {
            if (this.remitoSelected.length > 0) {
                let details = [];
                for (let i = 0; i < this.remitoSelected.length; i++) {
                    let find = this.$store.state.clientes.cliente.ventas.find(
                        e => e.id == this.remitoSelected[i]
                    );
                    for (let j = 0; j < find.articulos.length; j++) {
                        if (
                            find.articulos[j].pivot.cantidad -
                                find.articulos[j].pivot.cantidadfacturado >
                            0
                        ) {
                            details.push(find.articulos[j].pivot);
                        }
                    }
                }

                this.$store
                    .dispatch("facturas/facturar", {
                        details: details
                    })
                    .then(() => {
                        this.$router.push("/facturas/create");
                    });
            }
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

        cancelDeleteRemito() {
            this.remitoDeleteId = null;
            this.remitoDeleteDialog = false;
        },

        async deleteRemito() {
            this.inProcess = true;
            await this.$store.dispatch("remitos/destroy", { id: this.remitoDeleteId });
            await this.$store.dispatch("clientes/show", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            this.inProcess = false;
            this.remitoDeleteDialog = false;
        },

        // FACTURAS
        facturasOpenDeleteDialog(id) {
            this.facturasDeleteId = id;
            this.facturasDeleteDialog = true;
        },

        cancelDeleteFacturas() {
            this.facturasDeleteId = null;
            this.facturasDeleteDialog = false;
        },

        async deleteFactura() {
            this.inProcess = true;
            await this.$store.dispatch("facturas/destroy", { id: this.facturasDeleteId });
            await this.$store.dispatch("clientes/show", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            this.inProcess = false;
            this.facturasDeleteDialog = false;
        },

        // ENTREGAS
        printEntrega(id) {
            this.$store.dispatch("PDF/printEntrega", { id: id });
        },

        entregasOpenDeleteDialog(id) {
            this.entregasDeleteId = id;
            this.entregasDeleteDialog = true;
        },

        cancelDeleteEntregas() {
            this.entregasDeleteId = null;
            this.entregasDeleteDialog = false;
        },

        async deleteEntrega() {
            this.inProcess = true;
            await this.$store.dispatch("entregas/destroy", {
                id: this.entregasDeleteId
            });
            await this.$store.dispatch("clientes/show", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            this.inProcess = false;
            this.entregasDeleteDialog = false;
        },

        checkRole(roles) {
            if(this.$store.state.auth.user) {
                if(roles.find(e => e == this.$store.state.auth.user.rol)) {
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