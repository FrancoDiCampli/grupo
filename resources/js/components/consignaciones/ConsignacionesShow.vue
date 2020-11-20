<template>
    <div>
        <div v-if="$store.state.consignaciones.consignacion">
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123">
                    <v-card-text class="pa-0 black--text">
                        <div class="print-button" @click="print()">
                            <v-icon>fas fa-print</v-icon>
                        </div>
                        <v-row>
                            <v-col cols="12">
                                <h2 class="text-center mb-3">Consignacion</h2>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-left">
                                <h2 class="text-center">
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.nombrefantasia
                                    }}
                                </h2>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio comercial:</b>
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.domiciliocomercial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.condicioniva
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">Consignacion</h2>
                                <p>
                                    <b>Punto de venta:</b>
                                    0000{{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.puntoventa
                                    }}
                                    <b>Comprobante Nº:</b>
                                    {{
                                    $store.state.consignaciones.consignacion.consignacion
                                    .numconsignacion
                                    }}
                                </p>
                                <p>
                                    <b>Fecha de emisión:</b>
                                    {{
                                    $store.state.consignaciones.consignacion.consignacion
                                    .fecha
                                    }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Ingresos brutos:</b>
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Inicio de actividades:</b>
                                    {{
                                    $store.state.consignaciones.consignacion
                                    .configuracion.inicioactividades
                                    }}
                                </p>
                                <p>
                                    <b>Comprobante adherido:</b>
                                    {{
                                    $store.state.consignaciones.consignacion.consignacion
                                    .comprobanteadherido
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" class="pre-body">
                                <v-divider></v-divider>
                                <br />
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.consignaciones.consignacion.dependencia
                                    .name
                                    }}
                                </p>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12">
                                <v-simple-table class="detail-table mx-5">
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th class="hidden-sm-and-down">Cod. Producto</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Cantidad litros</th>
                                                <th class="hidden-sm-and-down">U. Medida</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle,
                                                index) in $store.state.consignaciones
                                                    .consignacion.detalles"
                                                :key="index"
                                            >
                                                <th
                                                    class="hidden-sm-and-down"
                                                >{{ detalle.codarticulo }}</th>
                                                <th>{{ detalle.articulo }}</th>
                                                <th>{{ detalle.cantidad }}</th>
                                                <th>{{ detalle.cantidadLitros }}</th>
                                                <th class="hidden-sm-and-down">{{ detalle.medida }}</th>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12" class="comprobantes-footer">
                                <div
                                    v-if="
                                        $store.state.consignaciones.consignacion.consignacion
                                            .observaciones
                                    "
                                >
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>
                                        {{
                                        $store.state.consignaciones.consignacion
                                        .consignacion.observaciones
                                        }}
                                    </p>
                                </div>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-row>
        </div>
        <div v-else>
            <v-row justify="center" style="margin-top: 200px;">
                <v-progress-circular :size="70" :width="7" color="primary" indeterminate></v-progress-circular>
            </v-row>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            inProcess: false,
        };
    },

    props: ["id"],

    mounted() {
        this.getConsignacion();
    },

    methods: {
        getConsignacion: async function () {
            this.inProcess = true;
            await this.$store.dispatch("consignaciones/show", {
                id: this.id,
            });
            this.inProcess = false;
        },

        print() {
            this.$store.dispatch("PDF/printConsignacion", { id: this.$store.state.consignaciones.consignacion.consignacion.id });
        },
    },
};
</script>

<style>

</style>