<template>
    <div>
        <div v-if="$store.state.entregas.entrega">
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123">
                    <v-card-text class="pa-0 black--text">
                        <div class="print-button" @click="print()">
                            <v-icon>fas fa-print</v-icon>
                        </div>
                        <v-row>
                            <v-col cols="12">
                                <h2 class="text-center mb-3">Entrega</h2>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-left">
                                <h2 class="text-center">
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.nombrefantasia
                                    }}
                                </h2>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio comercial:</b>
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.domiciliocomercial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.condicioniva
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">Entrega</h2>
                                <p>
                                    <b>Punto de venta:</b>
                                    0000{{
                                    $store.state.entregas.entrega
                                    .configuracion.puntoventa
                                    }}
                                    <b>Comprobante Nº:</b>
                                    {{
                                    $store.state.entregas.entrega.entrega
                                    .numentrega
                                    }}
                                </p>
                                <p>
                                    <b>Fecha de emisión:</b>
                                    {{
                                    $store.state.entregas.entrega.entrega
                                    .fecha
                                    }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Ingresos brutos:</b>
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Inicio de actividades:</b>
                                    {{
                                    $store.state.entregas.entrega
                                    .configuracion.inicioactividades
                                    }}
                                </p>
                                <p>
                                    <b>Comprobante adherido:</b>
                                    {{
                                    $store.state.entregas.entrega.entrega
                                    .comprobanteadherido
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" class="pre-body">
                                <v-divider></v-divider>
                                <br />
                                <p>
                                    <b>CUIT:</b>
                                    {{
                                    $store.state.entregas.entrega.cliente
                                    .documentounico
                                    }}
                                </p>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.entregas.entrega.cliente
                                    .razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.entregas.entrega.cliente
                                    .condicioniva
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio:</b>
                                    {{
                                    $store.state.entregas.entrega.cliente
                                    .direccion
                                    }}
                                </p>
                                <p>
                                    <b>Condición de venta:</b>
                                    {{
                                    $store.state.entregas.entrega.entrega
                                    .condicionventa
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
                                                index) in $store.state.entregas
                                                    .entrega.detalles"
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
                                        $store.state.entregas.entrega.entrega
                                            .observaciones
                                    "
                                >
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>
                                        {{
                                        $store.state.entregas.entrega
                                        .entrega.observaciones
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
        this.getEntrega();
    },

    methods: {
        getEntrega: async function () {
            this.inProcess = true;
            await this.$store.dispatch("entregas/show", {
                id: this.id,
            });
            this.inProcess = false;
        },

        print() {
            this.$store.dispatch("PDF/printEntrega", { id: this.$store.state.entregas.entrega.entrega.id });
        },
    },
};
</script>

<style lang="scss"></style>
