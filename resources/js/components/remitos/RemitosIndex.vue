<template>
    <div>
        <v-tooltip left v-if="selected.length > 0">
            <template v-slot:activator="{ on }">
                <v-btn
                    color="secondary"
                    dark
                    fab
                    fixed
                    right
                    bottom
                    large
                    v-on="on"
                    @click="facturar"
                >
                    <v-icon>fas fa-file-invoice-dollar</v-icon>
                </v-btn>
            </template>
            <span>Generar factura</span>
        </v-tooltip>

        <v-card shaped outlined :loading="$store.state.inProcess">
            <v-card-title>Remitos</v-card-title>
            <v-divider></v-divider>
            <v-card-text v-if="$store.state.remitos.remitos" class="px-0">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.remitos.remitos.remitos"
                    hide-default-footer
                    :items-per-page="-1"
                    :mobile-breakpoint="0"
                    :single-select="false"
                >
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>
                                <v-checkbox
                                    v-model="selected"
                                    :value="item.id"
                                    :disabled="isDisabled(item)"
                                    v-if="checkRole()"
                                ></v-checkbox>
                            </td>
                            <td class="hidden-xs-only">
                                {{ item.comprobanteadherido || item.numventa }}
                            </td>
                            <td>{{ item.cliente.razonsocial }}</td>
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
                                        <v-list-item @click="print(item.id)">
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
                                            v-if="checkOptions(item)"
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
                <slot></slot>
            </v-card-text>
        </v-card>

        <v-dialog v-model="deleteDialog" persistent width="450px">
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
                        @click="cancelDelete()"
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
import moment from "moment";
import FacturasIndex from "../facturas/FacturasIndex";

export default {
    data: () => ({
        deleteDialog: false,
        deleteId: null,
        headers: [
            { text: "", sortable: false },
            { text: "N°", sortable: false, class: "hidden-xs-only" },
            { text: "Cliente", sortable: false },
            { text: "Importe", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-sm-and-down" },
            { text: "", sortable: false }
        ],
        selected: []
    }),

    props: ["limit"],

    components: {
        FacturasIndex
    },

    methods: {
        facturar() {
            if (this.selected.length > 0) {
                let details = [];
                for (let i = 0; i < this.selected.length; i++) {
                    let find = this.$store.state.remitos.remitos.remitos.find(
                        e => e.id == this.selected[i]
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

        print(id) {
            this.$store.dispatch("PDF/printVenta", { id: id });
        },

        isDisabled(i) {
            if (this.selected.length > 0) {
                let find = this.$store.state.remitos.remitos.remitos.find(
                    e => e.id == this.selected[0]
                );
                return find.cliente.id == i.cliente.id ? i.todofacturado : true;
            } else {
                return i.todofacturado;
            }
        },

        checkOptions(item) {
            if(!item.hasFacturas && !item.hasEntregas && !item.hasPagos) {
                return true;
            } else {
                return false;
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

        openDeleteDialog(id) {
            this.deleteId = id;
            this.deleteDialog = true;
        },

        cancelDelete() {
            this.deleteId = null;
            this.deleteDialog = false;
        },

        async deleteRemito() {
            this.inProcess = true;
            await this.$store.dispatch("remitos/destroy", { id: this.deleteId });
            this.inProcess = false;
            this.$emit('erase');
            this.deleteDialog = false;
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

<style lang="scss"></style>
