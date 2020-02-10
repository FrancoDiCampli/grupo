<template>
    <div>
        <v-row justify="center">
            <v-col cols="5">
                <v-dialog
                    ref="ComprasDesde"
                    v-model="modalComprasDesde"
                    :return-value.sync="fechaComprasDesde"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="fechaComprasDesde"
                            label="Fecha Desde"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="fechaComprasDesde"
                        @change="getCompras()"
                        scrollable
                        locale="es"
                        format="YYYYMMDD"
                        color="primary"
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="modalComprasDesde = false">Cancel</v-btn>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.ComprasDesde.save(fechaComprasDesde)"
                        >OK</v-btn>
                    </v-date-picker>
                </v-dialog>
            </v-col>

            <v-col cols="5">
                <v-dialog
                    ref="ComprasHasta"
                    v-model="modalComprasHasta"
                    :return-value.sync="fechaComprasHasta"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="fechaComprasHasta"
                            label="Fecha Hasta"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="fechaComprasHasta"
                        @change="getCompras()"
                        scrollable
                        locale="es"
                        format="YYYYMMDD"
                        color="primary"
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="modalComprasHasta = false">Cancel</v-btn>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.ComprasHasta.save(fechaComprasHasta)"
                        >OK</v-btn>
                    </v-date-picker>
                </v-dialog>
            </v-col>
        </v-row>

        <v-tabs vertical :grow="true">
            <v-tab>Compras Por Fechas</v-tab>
            <v-tab>Compras Por Proveedores</v-tab>

            <v-tab-item>
                <ve-line
                    v-if="$store.state.reportes.compras"
                    :legend-visible="false"
                    :data="$store.state.reportes.compras.compras.comprasFechasChart"
                ></ve-line>
            </v-tab-item>
            <v-tab-item>
                <ve-pie
                    v-if="$store.state.reportes.compras"
                    :legend-visible="false"
                    :data="$store.state.reportes.compras.compras.comprasProveedores"
                ></ve-pie>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
export default {
    data() {
        return {
            modalComprasDesde: false,
            modalComprasHasta: false,
            fechaComprasDesde: null,
            fechaComprasHasta: null
        };
    },

    mounted() {
        this.getCompras();
    },

    methods: {
        getCompras: async function() {
            let response = await this.$store.dispatch("reportes/compras", {
                limit: 10,
                desde: this.fechaComprasDesde,
                hasta: this.fechaComprasHasta
            });
        }
    }
};
</script>

<style>
</style>