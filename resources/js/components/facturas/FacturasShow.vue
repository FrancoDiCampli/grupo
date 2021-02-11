<template>
    <div>
        <div v-if="$store.state.facturas.factura">
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123">
                    <v-card-text class="pa-0 black--text">
                        <v-row>
                            <v-col cols="12">
                                <h2 class="text-center mb-3">FACTURA</h2>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-left">
                                <h2 class="text-center">
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.nombrefantasia
                                    }}
                                </h2>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio comercial:</b>
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.domiciliocomercial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.condicioniva
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">FACTURA</h2>
                                <p>
                                    <b>Punto de venta:</b>
                                    0000{{
                                    $store.state.facturas.factura
                                    .configuracion.puntoventa
                                    }}
                                    <b>Comprobante Nº:</b>
                                    {{
                                    $store.state.facturas.factura.factura
                                    .numfactura
                                    }}
                                </p>
                                <p>
                                    <b>Comprobante adherido:</b>
                                    {{
                                    $store.state.facturas.factura.factura
                                    .comprobanteadherido
                                    }}
                                </p>
                                <p>
                                    <b>Fecha de emisión:</b>
                                    {{
                                    $store.state.facturas.factura.factura
                                    .fecha | formatDate
                                    }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Ingresos brutos:</b>
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Inicio de actividades:</b>
                                    {{
                                    $store.state.facturas.factura
                                    .configuracion.inicioactividades
                                    }}
                                </p>
                                
                            </v-col>
                            <v-col cols="12" class="pre-body">
                                <v-divider></v-divider>
                                <br />
                                <p>
                                    <b>CUIT:</b>
                                    {{
                                    $store.state.facturas.factura.cliente
                                    .documentounico
                                    }}
                                </p>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.facturas.factura.cliente
                                    .razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.facturas.factura.cliente
                                    .condicioniva
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio:</b>
                                    {{
                                    $store.state.facturas.factura.cliente
                                    .direccion
                                    }}
                                </p>
                                <p>
                                    <b>Condición de venta:</b>
                                    {{
                                    $store.state.facturas.factura.factura
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
                                                <th>Precio unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle,
                                                index) in $store.state.facturas
                                                    .factura.detalles"
                                                :key="index"
                                            >
                                                <th
                                                    class="hidden-sm-and-down"
                                                >{{ detalle.codarticulo }}</th>
                                                <th>{{ detalle.articulo }}</th>
                                                <th>{{ detalle.cantidad }}</th>
                                                <th>{{ detalle.cantidadLitros }}</th>
                                                <th class="hidden-sm-and-down">{{ detalle.medida }}</th>
                                                <th>{{ detalle.preciounitario | formatCurrency('USD') }}</th>
                                                <th>{{ detalle.subtotal | formatCurrency('USD') }}</th>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12" class="comprobantes-footer">
                                <div
                                    v-if="
                                        $store.state.facturas.factura.factura
                                            .observaciones
                                    "
                                >
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>
                                        {{
                                        $store.state.facturas.factura
                                        .factura.observaciones
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <v-divider></v-divider>
                                    <br />
                                    <p>
                                        <b>Cotización:</b>
                                        $
                                        {{
                                        $store.state.facturas.factura.factura
                                        .cotizacion
                                        }}
                                        <b>Fecha:</b>
                                        {{
                                        $store.state.facturas.factura.factura
                                        .fechaCotizacion | formatDate
                                        }}
                                    </p>
                                    <p>
                                        <b>Total en pesos:</b>
                                        {{
                                        $store.state.facturas.factura.factura
                                        .totalPesos | formatCurrency('ARS')
                                        }}
                                    </p>
                                </div>

                                <v-divider></v-divider>
                                <br />
                                <div class="footer-final">
                                    <p>
                                        <b>Subtotal:</b>
                                        {{
                                        $store.state.facturas.factura
                                        .factura.subtotal | formatCurrency('USD')
                                        }}
                                    </p>
                                    <p>
                                        <b>Bonificación:</b>
                                        {{
                                        $store.state.facturas.factura
                                        .factura.bonificacion
                                        }}%
                                    </p>
                                    <p>
                                        <b>Recargo:</b>
                                        {{
                                        $store.state.facturas.factura
                                        .factura.recargo
                                        }}%
                                    </p>
                                    <p>
                                        <b>IVA 21%:</b>
                                        U$D
                                        {{
                                        $store.state.facturas.factura
                                        .factura.iva
                                        }}
                                    </p>
                                    <p>
                                        <b>Total:</b>
                                        {{
                                        $store.state.facturas.factura
                                        .factura.total | formatCurrency('USD')
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
        this.getFactura();
    },

    methods: {
        getFactura: async function () {
            this.inProcess = true;
            await this.$store.dispatch("facturas/show", {
                id: this.id,
            });
            this.inProcess = false;
        },
    },
};
</script>

<style lang="scss"></style>
