<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ReportesIndex :process="inProcess">
                        <template slot="filter">
                            <v-row>
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-dialog
                                        ref="dialogFechaDesde"
                                        v-model="dialogs.desde"
                                        :return-value.sync="fechaDesde"
                                        persistent
                                        :width="
                                            $vuetify.breakpoint.xsOnly
                                                ? '100%'
                                                : '300px'
                                        "
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                :value="fechaDesde | formatDate"
                                                @input="
                                                    value =>
                                                        (fechaDesde = value)
                                                "
                                                label="Fecha desde"
                                                readonly
                                                outlined
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="fechaDesde"
                                            scrollable
                                            locale="es"
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="
                                                    $refs.dialogFechaDesde.save(
                                                        fechaDesde
                                                    )
                                                "
                                                >Aceptar</v-btn
                                            >
                                        </v-date-picker>
                                    </v-dialog>
                                </v-col>
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-dialog
                                        ref="dialogFechaHasta"
                                        v-model="dialogs.hasta"
                                        :return-value.sync="fechaHasta"
                                        persistent
                                        :width="
                                            $vuetify.breakpoint.xsOnly
                                                ? '100%'
                                                : '300px'
                                        "
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                :value="fechaHasta | formatDate"
                                                @input="
                                                    value =>
                                                        (fechaHasta = value)
                                                "
                                                label="Fecha hasta"
                                                readonly
                                                outlined
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="fechaHasta"
                                            scrollable
                                            locale="es"
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="
                                                    $refs.dialogFechaHasta.save(
                                                        fechaHasta
                                                    )
                                                "
                                                >Aceptar</v-btn
                                            >
                                        </v-date-picker>
                                    </v-dialog>
                                </v-col>
                            </v-row>
                        </template>
                    </ReportesIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import moment from "moment";
import ReportesIndex from "../../components/reportes/ReportesIndex.vue";

export default {
    data: () => ({
        inProcess: false,
        dialogs: {
            desde: false,
            hasta: false
        },
        fechaDesde: moment().format("YYYY-MM-DD"),
        fechaHasta: moment().format("YYYY-MM-DD")
    }),

    components: {
        ReportesIndex
    },

    watch: {
        fechaDesde() {
            this.getData();
        },

        fechaHasta() {
            this.getData();
        }
    },

    mounted() {
        this.getData();
    },

    methods: {
        getData: async function() {
            this.inProcess = true;
            await this.getReports();
            this.inProcess = false;
        },

        getReports: async function() {
            // OBTENER VENTAS
            await this.$store.dispatch("reportes/ventas", {
                desde: this.fechaDesde,
                hasta: this.fechaHasta
            });

            // OBTENER COMPRAS
            await this.$store.dispatch("reportes/compras", {
                desde: this.fechaComprasDesde,
                hasta: this.fechaComprasHasta
            });

            // OBTENER PRODUCTOS
            await this.$store.dispatch("reportes/detallesCompras", {
                desde: this.fechaDesde,
                hasta: this.fechaHasta
            });

            await this.$store.dispatch("reportes/detallesVentas", {
                desde: this.fechaDesde,
                hasta: this.fechaHasta
            });
        }
    }
};
</script>

<style></style>
