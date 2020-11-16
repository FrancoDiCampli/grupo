<template>
    <div>
        <v-card shaped outlined :loading="$store.state.inProcess">
            <v-card-title>Facturas</v-card-title>
            <v-divider></v-divider>
            <v-card-text v-if="$store.state.facturas.facturas" class="px-0">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.facturas.facturas.facturas"
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
                            <td>{{ item.cliente.razonsocial }}</td>
                            <td class="hidden-xs-only">{{ item.fecha }}</td>
                            <td>{{ item.total }}</td>
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
                                            v-if="!item.hasPagos"
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
                    Se eliminará la factura seleccionada, esta acción es irreversible.
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
                        @click="deleteFactura()"
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
        headers: [
            { text: "N°", sortable: false },
            { text: "Nombre/Apellido", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-xs-only" },
            { text: "Importe", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"],

    methods: {
        openDeleteDialog(id) {
            this.deleteId = id;
            this.deleteDialog = true;
        },

        cancelDelete() {
            this.deleteId = null;
            this.deleteDialog = false;
        },

        async deleteFactura() {
            this.inProcess = true;
            await this.$store.dispatch("facturas/destroy", { id: this.deleteId });
            this.inProcess = false;
            this.$emit('erase');
            this.deleteDialog = false;
        }

    }
};
</script>

<style></style>
