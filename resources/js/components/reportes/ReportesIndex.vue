<template>
    <div>
        <v-tabs grow slider-color="secondary" active-class="secondary--text">
            <v-tabs-slider></v-tabs-slider>
            <v-tab>Reportes Ventas</v-tab>
            <v-tab>Reportes Compras</v-tab>
            <v-tab>Reportes Productos</v-tab>

            <v-tab-item>
                <ReportesVentas />
            </v-tab-item>
            <v-tab-item>
                <ReportesCompras />
            </v-tab-item>
            <v-tab-item>
                <ReportesProductos />
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
import ReportesVentas from "./ReportesVentas.vue";

import ReportesCompras from "./ReportesCompras.vue";

import ReportesProductos from "./ReportesProductos.vue";

export default {
    name: "ReportesIndex",

    components: {
        ReportesVentas,
        ReportesCompras,
        ReportesProductos
    }
};
</script>

<style>
</style>