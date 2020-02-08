<template>
    <div>
        <v-tabs right hide-slider background-color="transparent">
            <v-btn
                color="primary"
                class="filter-btn"
                icon
                @click="filterMenu = !filterMenu"
            >
                <v-icon size="medium">fas fa-filter</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-tab>Ventas</v-tab>
            <v-tab>Facturas</v-tab>
            <v-tab-item>
                <v-expand-transition>
                    <div class="filters" v-if="filterMenu">
                        <slot name="filter"></slot>
                    </div>
                </v-expand-transition>

                <v-card shaped outlined :loading="$store.state.inProcess">
                    <v-card-title>Ventas</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text v-if="$store.state.ventas.ventas" class="px-0">
                        <v-data-table
                            :headers="headers"
                            :items="$store.state.ventas.ventas.ventas"
                            hide-default-footer
                            :items-per-page="limit"
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
                                                item.numfactura != null ||
                                                    item.pagada != true
                                            "
                                        ></v-checkbox>
                                    </td>
                                    <td class="hidden-xs-only">
                                        {{ item.numventa }}
                                    </td>
                                    <td>{{ item.cliente.razonsocial }}</td>
                                    <td>{{ item.total }}</td>
                                    <td class="hidden-sm-and-down">
                                        {{ item.fecha }}
                                    </td>
                                    <td class="hidden-sm-and-down">
                                        {{ item.condicionventa }}
                                    </td>
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
                                                        >fas
                                                        fa-ellipsis-v</v-icon
                                                    >
                                                </v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item
                                                    :to="
                                                        `/ventas/show/${item.id}`
                                                    "
                                                >
                                                    <v-list-item-title
                                                        >Detalles</v-list-item-title
                                                    >
                                                </v-list-item>
                                                <v-list-item
                                                    @click="print(item.id)"
                                                >
                                                    <v-list-item-title
                                                        >Imprimir</v-list-item-title
                                                    >
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                        <slot name="loadMore"></slot>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-expand-transition>
                    <div class="filters" v-if="filterMenu">
                        <slot name="filter"></slot>
                    </div>
                </v-expand-transition>
                <v-card shaped outlined :loading="$store.state.inProcess">
                    <v-card-title>Facturas</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text
                        v-if="$store.state.facturas.facturas"
                        class="px-0"
                    >
                        <FacturasIndex :limit="limit"></FacturasIndex>
                        <slot name="loadMore"></slot>
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
        filterMenu: false,
        headers: [
            { text: "", sortable: false },
            { text: "Número", sortable: false, class: "hidden-xs-only" },
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
                await localStorage.setItem(
                    "facturas",
                    JSON.stringify({
                        seleccionadas: this.selected
                    })
                );
                this.$router.push("/facturas/create");
            } else {
                this.alert = true;
            }
        },

        print(id) {
            axios({
                url: "/api/ventasPDF/" + id,
                method: "GET",
                responseType: "blob"
            }).then(response => {
                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "factura" + id + ".pdf");
                document.body.appendChild(link);
                link.click();
            });
        }
    }
};
</script>

<style lang="scss">
.filter-btn {
    margin-top: 4px;
    margin-left: 12px;
}

.filters {
    height: auto;
}
</style>
