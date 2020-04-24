<template>
    <div>
        <v-tabs right hide-slider background-color="transparent">
            <v-spacer></v-spacer>
            <v-tab>Ventas</v-tab>
            <v-tab>Facturas</v-tab>
            <v-tab-item>
                <v-card shaped outlined :loading="$store.state.inProcess">
                    <v-card-title>Ventas</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text v-if="$store.state.ventas.ventas" class="px-0">
                        <v-data-table
                            :headers="headers"
                            :items="$store.state.ventas.ventas.ventas"
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
                                            :disabled="
                                                item.numfactura ||
                                                    !item.pagada
                                            "
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
                                                <v-list-item
                                                    :to="
                                                        `/ventas/show/${item.id}`
                                                    "
                                                >
                                                    <v-list-item-title>Detalles</v-list-item-title>
                                                </v-list-item>
                                                <v-list-item @click="print(item.id)">
                                                    <v-list-item-title>Imprimir</v-list-item-title>
                                                </v-list-item>
                                                <v-list-item
                                                    @click="erase(item.id)"
                                                    :disabled="canBeErased(item)"
                                                >
                                                    <v-list-item-title>Anular</v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                        <br />
                        <v-row justify="center">
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="selected.length <= 0"
                                @click="facturar()"
                                color="secondary"
                                class="mr-3"
                                outlined
                                tile
                            >Facturar</v-btn>
                            <slot></slot>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card shaped outlined :loading="$store.state.inProcess">
                    <v-card-title>Facturas</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text v-if="$store.state.facturas.facturas" class="px-0">
                        <FacturasIndex :limit="limit"></FacturasIndex>
                        <br />
                        <v-row justify="center" v-if="$store.state.ventas.ventas">
                            <slot></slot>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>
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
            { text: "", sortable: false }
        ],
        selected: []
    }),

    props: ["limit"],

    components: {
        FacturasIndex
    },

    methods: {
        facturar: async function() {
            if (this.selected.length > 0) {
                this.$store
                    .dispatch("facturas/facturar", {
                        selected: this.selected
                    })
                    .then(() => {
                        this.$router.push("/facturas/create");
                    });
            }
        },

        print(id) {
            this.$store.dispatch("PDF/printVenta", { id: id });
        },

        canBeErased(item) {
            if (!item.numfactura) {
                if (item.cuenta) {
                    if (item.cuenta.pagos.length > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        },

        erase(id) {
            this.$store.dispatch("ventas/destroy", { id: id });
            this.$emit("erase", true);
        }
    }
};
</script>

<style lang="scss"></style>
