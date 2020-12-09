<template>
    <div>
        <v-tabs right active-class="piymary--text" hide-slider>
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
            <v-tab>Compras</v-tab>
            <v-tab>Articulos</v-tab>

            <v-tab-item>
                <v-expand-transition>
                    <div class="filters" v-if="filterMenu">
                        <slot name="filter"></slot>
                    </div>
                </v-expand-transition>
                <v-card shaped outlined :loading="process">
                    <v-card-text class="pa-0">
                        <div
                            @click="exportarVentas()"
                            class="print-button excel"
                            v-if="ventasPrint"
                        >
                            <v-icon>fas fa-file-excel</v-icon>
                        </div>
                        <br />
                        <ReportesVentas
                            ref="reportesVentas"
                            @changetab="checkPrint($event)"
                            :desde="desde"
                            :hasta="hasta"
                        ></ReportesVentas>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-expand-transition>
                    <div class="filters" v-if="filterMenu">
                        <slot name="filter"></slot>
                    </div>
                </v-expand-transition>
                <v-card shaped outlined :loading="process">
                    <v-card-text class="pa-0">
                        <div
                            @click="exportarVentas()"
                            class="print-button excel"
                        >
                            <v-icon>fas fa-file-excel</v-icon>
                        </div>
                        <ReportesCompras></ReportesCompras>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-expand-transition>
                    <div class="filters" v-if="filterMenu">
                        <slot name="filter"></slot>
                    </div>
                </v-expand-transition>
                <v-card shaped outlined :loading="process">
                    <v-card-text class="pa-0">
                        <div
                            @click="exportarVentas()"
                            class="print-button excel"
                        >
                            <v-icon>fas fa-file-excel</v-icon>
                        </div>
                        <ReportesProductos></ReportesProductos>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
import axios from "axios";
import ReportesVentas from "./ReportesVentas.vue";
import ReportesCompras from "./ReportesCompras.vue";
import ReportesProductos from "./ReportesProductos.vue";

export default {
    data: () => ({
        filterMenu: false,
        ventasPrint: true
    }),

    props: ["process", "desde", "hasta"],

    components: {
        ReportesVentas,
        ReportesCompras,
        ReportesProductos
    },

    methods: {
        checkPrint(e) {
            this.ventasPrint = e != 3;
        },

        exportarVentas() {
            axios({
                url: "/api/reportes/ventas/export",
                method: "POST",
                responseType: "blob",
                data: {
                    desde: "2020-11-15",
                    hasta: "2020-12-15"
                }
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "reportesVentas.xlsx");
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.log(error);
                });
        },
        exportarArticulos() {
            axios({
                url: "/api/reportes/articulos/export",
                method: "POST",
                responseType: "blob",
                data: {
                    desde: "2020-11-15",
                    hasta: "2020-12-15"
                }
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "reportesArticulos.xlsx");
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.log(error);
                });
        },
        exportarCompras() {
            axios({
                url: "/api/reportes/compras/export",
                method: "POST",
                responseType: "blob",
                data: {
                    desde: "2020-11-15",
                    hasta: "2020-12-15"
                }
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "reportesCompras.xlsx");
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.log(error);
                });
        }
    }
};
</script>

<style></style>
