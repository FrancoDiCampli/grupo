<template>
    <div>
        
        <v-tooltip left>
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
                    to="/pedidos/nuevo"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>
            </template>
            <span>Nueva Nota de Pedido</span>
        </v-tooltip>

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card shaped outlined :loading="$store.state.inProcess">
            <v-card-title>Notas de pedidos</v-card-title>
            <v-divider></v-divider>
            <v-card-text v-if="$store.state.pedidos.pedidos" class="px-0">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.pedidos.pedidos.pedidos"
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
                            <td>{{ item.cliente.razonsocial }}</td>
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
                                        <v-list-item @click="print(item.id)">
                                            <v-list-item-title
                                                >Imprimir</v-list-item-title
                                            >
                                        </v-list-item>
                                        <v-list-item
                                            v-if="item.numventa == null"
                                            :to="`/pedidos/editar/${item.id}`"
                                        >
                                            <v-list-item-title
                                                >Editar</v-list-item-title
                                            >
                                        </v-list-item>
                                        <v-list-item
                                            v-if="item.numventa == null"
                                            @click="preventSold(item.id)"
                                        >
                                            <v-list-item-title>
                                                Generar Venta
                                            </v-list-item-title>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="item.numventa == null"
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
                        <br />
                        <v-row
                            justify="center"
                            v-if="$store.state.pedidos.pedidos"
                        >
                            <br />
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="
                                    limit >= $store.state.pedidos.pedidos.total
                                "
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                                >Cargar Más</v-btn
                            >
                        </v-row>
                    </v-card-text>
        </v-card>

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
                    Se eliminará el pedido seleccionado, esta acción es irreversible.
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
                        @click="deletePedido()"
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

export default {
    data: () => ({
        limit: 10,
        ventaID: null,
        preventDialog: false,
        remitoadherido: null,
        inProcess: false,
        deleteDialog: false,
        deleteId: null,
        headers: [
            { text: "Número", sortable: false },
            { text: "Nombre/Apellido", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-xs-only" },
            { text: "Importe", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getPedidos();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },

    methods: {
        getPedidos: async function() {
            await this.$store.dispatch("pedidos/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += this.limit;
            await this.getPedidos();
        },

        loadOnScroll() {
            if(document.body.scrollTop + document.body.clientHeight >= document.body.scrollHeight) {
                if(!this.$store.state.inProcess) {
                    this.loadMore();
                }
            }
        },

        print(id) {
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

        openDeleteDialog(id) {
            this.deleteId = id;
            this.deleteDialog = true;
        },

        cancelDelete() {
            this.deleteId = null;
            this.deleteDialog = false;
        },

        async deletePedido() {
            this.inProcess = true;
            await this.$store.dispatch("pedidos/destroy", { id: this.deleteId });
            this.inProcess = false;
            this.getPedidos();
            this.deleteDialog = false;
        }
    }
};
</script>

<style>
.filter-btn-pedidos {
    margin-top: 4px;
    margin-left: 24px;
}

.filters {
    height: auto;
}
</style>
