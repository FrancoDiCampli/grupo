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
                                ></v-checkbox>
                            </td>
                            <td class="hidden-xs-only">{{ item.comprobanteadherido }}</td>
                            <td>{{ item.cliente.razonsocial }}</td>
                            <td>{{ item.total }}</td>
                            <td class="hidden-sm-and-down">{{ item.fecha }}</td>
                            <td class="hidden-sm-and-down">{{ item.condicionventa }}</td>
                            <td>
                                <v-menu offset-y>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="secondary" text icon v-on="on">
                                            <v-icon size="medium">
                                                fas
                                                fa-ellipsis-v
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item :to="`/remitos/show/${item.id}`">
                                            <v-list-item-title>Detalles</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="print(item.id)">
                                            <v-list-item-title>Imprimir</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item v-if="!item.todoentregado">
                                            <v-list-item-title>Generar entrega</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import FacturasIndex from "../facturas/FacturasIndex";

export default {
    data: () => ({
        headers: [
            { text: "", sortable: false },
            { text: "N° Adhe.", sortable: false, class: "hidden-xs-only" },
            { text: "Cliente", sortable: false },
            { text: "Importe", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-sm-and-down" },
            { text: "Condición", sortable: false, class: "hidden-sm-and-down" },
            { text: "", sortable: false },
        ],
        selected: [],
    }),

    props: ["limit"],

    components: {
        FacturasIndex,
    },

    methods: {
        facturar() {
            if (this.selected.length > 0) {
                let details = [];
                for (let i = 0; i < this.selected.length; i++) {
                    let find = this.$store.state.remitos.remitos.remitos.find(
                        (e) => e.id == this.selected[i]
                    );
                    for (let j = 0; j < find.articulos.length; j++) {
                        details.push(find.articulos[j].pivot);
                    }
                }

                this.$store
                    .dispatch("facturas/facturar", {
                        details: details,
                    })
                    .then(() => {
                        this.$router.push("/facturas/create");
                    });
            }
        },

        print(id) {
            this.$store.dispatch("PDF/printVenta", { id: id });
        },

        isDisabled(i) {
            if (this.selected.length > 0) {
                let find = this.$store.state.remitos.remitos.remitos.find(
                    (e) => e.id == this.selected[0]
                );
                return find.cliente.id == i.cliente.id ? i.todofacturado : true;
            } else {
                return i.todofacturado;
            }
        },
    },
};
</script>

<style lang="scss"></style>
