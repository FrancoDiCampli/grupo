<template>
    <div>
        <div>
            <div v-if="recibo">
                <v-row justify="center">
                    <v-card
                        shaped
                        outlined
                        width="794"
                        height="1123"
                        :loading="$store.state.inProcess"
                    >
                        <v-card-text class="pa-0 black--text">
                            <div class="print-button" @click="print()">
                                <v-icon>fas fa-print</v-icon>
                            </div>
                            <v-row>
                                <v-col cols="12">
                                    <h2 class="text-center mb-3">RECIBO X</h2>
                                    <v-divider></v-divider>
                                </v-col>

                                <v-col cols="12" sm="6" class="header-left">
                                    <h2 class="text-center">
                                        {{
                                        recibo.configuracion.nombrefantasia
                                        }}
                                    </h2>
                                    <p>
                                        <b>Razón Social:</b>
                                        {{ recibo.configuracion.razonsocial }}
                                    </p>
                                    <p>
                                        <b>Domicilio Comercial:</b>
                                        {{
                                        recibo.configuracion
                                        .domiciliocomercial
                                        }}
                                    </p>
                                    <p>
                                        <b>Condición Frente al IVA:</b>
                                        {{ recibo.configuracion.condicioniva }}
                                    </p>
                                </v-col>
                                <v-col cols="12" sm="6" class="header-right">
                                    <h2 class="text-center">RECIBO</h2>
                                    <p>
                                        <b>Punto de Venta:</b>
                                        0000{{
                                        recibo.configuracion.puntoventa
                                        }}
                                        <b>Comprobante Nº:</b>
                                        {{ recibo.recibo.numrecibo }}
                                    </p>
                                    <p>
                                        <b>Fecha de Emisión:</b>
                                        {{ recibo.recibo.fecha | formatDate }}
                                    </p>
                                    <p>
                                        <b>Cuit:</b>
                                        {{ recibo.configuracion.cuit }}
                                    </p>
                                    <p>
                                        <b>Ingresos Brutos:</b>
                                        {{ recibo.configuracion.cuit }}
                                    </p>
                                    <p>
                                        <b>Inicio de Actividades:</b>
                                        {{
                                        recibo.configuracion
                                        .inicioactividades
                                        }}
                                    </p>
                                </v-col>
                                <v-col cols="12" class="pre-body">
                                    <v-divider></v-divider>
                                    <br />
                                    <p>
                                        <b>CUIT:</b>
                                        {{ recibo.cliente.documentounico }}
                                    </p>
                                    <p>
                                        <b>Razón Social:</b>
                                        {{ recibo.cliente.razonsocial }}
                                    </p>
                                    <p>
                                        <b>Domicilio:</b>
                                        {{ recibo.cliente.direccion }}
                                    </p>
                                    <v-divider></v-divider>
                                </v-col>
                                <v-col cols="12">
                                    <v-simple-table class="detail-table mx-5">
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th>Pago Nº</th>
                                                    <th>Importe</th>
                                                    <th>Fecha</th>
                                                    <th>Método de Pago</th>
                                                    <th>En Pesos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(pago,
                                                    index) in recibo.pagos"
                                                    :key="index"
                                                >
                                                    <th>{{ pago.numpago }}</th>
                                                    <th>{{ pago.importe | formatCurrency('USD') }}</th>
                                                    <th>{{ pago.fecha | formatDate }}</th>
                                                    <th>{{ pago.forma[0] }}</th>
                                                    <th>
                                                        $
                                                        {{
                                                        pago.forma[1].pesos
                                                        }}
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                </v-col>
                                <v-col cols="12" class="comprobantes-footer">
                                    <v-divider></v-divider>
                                    <br />
                                    <div class="footer-final">
                                        <p>
                                            <b>Cotización:</b>
                                            {{ recibo.recibo.cotizacion | formatCurrency('USD') }}
                                        </p>
                                        <p>
                                            <b>Fecha Cotización:</b>
                                            {{ recibo.recibo.fecha_cotizacion | formatDate }}
                                        </p>
                                        <v-divider></v-divider>
                                        <br />
                                        <p>
                                            <b>Total:</b>
                                            USD ${{ recibo.recibo.total }}
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
    </div>
</template>

<script>
export default {
    data() {
        return {
            inProcess: false,
            recibo: null
        };
    },

    props: ["id"],

    mounted() {
        this.getRecibo();
    },

    methods: {
        getRecibo: async function() {
            this.inProcess = true;
            axios
                .get("/api/showRecibo/" + this.id)
                .then(response => {
                    this.recibo = response.data;
                })
                .catch(error => {
                    console.log(error);
                });
            this.inProcess = false;
        },

        print() {
            let id = this.recibo.recibo.id;
            this.$store.dispatch("PDF/printRecibo", { id: id });
        }
    }
};
</script>

<style lang="scss"></style>
