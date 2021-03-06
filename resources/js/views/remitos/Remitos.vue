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

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10">
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
                                    <tr :class="item.todoentregado && item.todofacturado ? 'disabled--text' : ''">
                                        <td>
                                            <v-checkbox
                                                v-model="selected"
                                                :value="item.id"
                                                :disabled="isDisabled(item)"
                                                v-if="checkRole()"
                                            ></v-checkbox>
                                        </td>
                                        <td class="hidden-sm-and-down">
                                            <v-tooltip top>
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-icon 
                                                        v-bind="attrs" 
                                                        v-on="on"
                                                        :color="hasEntregasColor(item)"
                                                    >fas fa-truck</v-icon>
                                                </template>
                                                <span>
                                                    <div v-if="item.todoentregado">Posee entregas totales</div>
                                                    <div v-else-if="item.hasEntregas">Posee entregas parciales</div>
                                                    <div v-else>No posee entregas</div>
                                                </span>
                                            </v-tooltip>
                                        </td>
                                        <td class="hidden-sm-and-down" v-if="checkRole()">   
                                            <v-tooltip top>
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-icon 
                                                        v-bind="attrs" 
                                                        v-on="on"
                                                        :color="hasFacturasColor(item)"
                                                    >fas fa-dollar-sign</v-icon>
                                                </template>
                                                <span>
                                                    <div v-if="item.todofacturado">Posee facturas totales</div>
                                                    <div v-else-if="item.hasFacturas">Posee facturas parciales</div>
                                                    <div v-else>No posee facturas</div>
                                                </span>
                                            </v-tooltip>    
                                        </td>
                                        <td class="hidden-xs-only">
                                            {{ item.comprobanteadherido || item.numventa }}
                                        </td>
                                        <td>{{ item.cliente.razonsocial }}</td>
                                        <td>{{ item.total | formatCurrency('USD') }}</td>
                                        <td class="hidden-sm-and-down">{{ item.fecha | formatDate }}</td>
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
                                                        v-if="checkOptions(item) && checkRole()"
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
                            <div v-if="$store.state.remitos.remitos">
                                <br />
                                <v-row justify="center" v-if="$store.state.remitos.remitos">
                                    <br />
                                    <v-btn
                                        :loading="$store.state.inProcess"
                                        :disabled="limit >= $store.state.remitos.remitos.total"
                                        @click="loadMore()"
                                        color="secondary"
                                        outlined
                                        tile
                                    >Cargar Más</v-btn>
                                </v-row>
                            </div>
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
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import moment from "moment";

export default {
    data: () => ({
        limit: 10,
        inProcess: false,
        deleteDialog: false,
        deleteId: null,
        headers: [
            { text: "", sortable: false },
            { text: "", sortable: false, class: 'hidden-sm-and-down' },
            { text: "", sortable: false, class: 'hidden-sm-and-down' },
            { text: "N°", sortable: false, class: "hidden-xs-only" },
            { text: "Cliente", sortable: false },
            { text: "Importe", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-sm-and-down" },
            { text: "", sortable: false }
        ],
        selected: []
    }),

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getRemitos();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },

    methods: {
        getRemitos: async function () {
            await this.$store.dispatch("remitos/index", {
                limit: this.limit,
            });
        },

        loadMore: async function () {
            this.limit += 10;
            await this.getRemitos();
        },

        loadOnScroll() {
            if(document.body.scrollTop + document.body.clientHeight >= document.body.scrollHeight) {
                if(!this.$store.state.inProcess) {
                    this.loadMore();
                }
            }
        },

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
                    fecha: item.fecha,
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
            this.getRemitos();
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
        },

        // ESTILOS
        hasEntregasColor(item) {
            if(item.todoentregado) {
                return '#787878';
            } else if(item.hasEntregas) {
                return 'warning';
            } else {
                return 'success';
            }
        },

        hasFacturasColor(item) {
            if(item.todofacturado) {
                return '#787878';
            } else if(item.hasFacturas) {
                return 'warning';
            } else {
                return 'success';
            }
        }
    },
};
</script>

<style>

</style>
