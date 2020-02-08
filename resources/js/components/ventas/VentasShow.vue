<template>
    <div>
        <div v-if="$store.state.ventas.venta">
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123">
                    <v-card-text class="pa-0 black--text">
                        <v-row>
                            <v-col cols="12">
                                <h2
                                    class="text-center mb-3"
                                >{{ $store.state.ventas.venta.factura.tipocomprobante }}</h2>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-left">
                                <h2
                                    class="text-center"
                                >{{ $store.state.ventas.venta.configuracion.nombrefantasia }}</h2>
                                <p>
                                    <b>Razón Social:</b>
                                    {{ $store.state.ventas.venta.configuracion.razonsocial }}
                                </p>
                                <p>
                                    <b>Domicilio Comercial:</b>
                                    {{ $store.state.ventas.venta.configuracion.domiciliocomercial }}
                                </p>
                                <p>
                                    <b>Condición Frente al IVA:</b>
                                    {{ $store.state.ventas.venta.configuracion.condicioniva }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">REMITO</h2>
                                <p>
                                    <b>Punto de Venta:</b>
                                    0000{{ $store.state.ventas.venta.configuracion.puntoventa }}
                                    <b>Comprobante Nº:</b>
                                    {{ $store.state.ventas.venta.factura.numventa }}
                                </p>
                                <p>
                                    <b>Fecha de Emisión:</b>
                                    {{ $store.state.ventas.venta.factura.fecha }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{ $store.state.ventas.venta.configuracion.cuit }}
                                </p>
                                <p>
                                    <b>Ingresos Brutos:</b>
                                    {{ $store.state.ventas.venta.configuracion.cuit }}
                                </p>
                                <p>
                                    <b>Inicio de Actividades:</b>
                                    {{ $store.state.ventas.venta.configuracion.inicioactividades }}
                                </p>
                            </v-col>
                            <v-col cols="12" class="pre-body">
                                <v-divider></v-divider>
                                <br />
                                <p>
                                    <b>CUIT:</b>
                                    {{$store.state.ventas.venta.cliente.documentounico}}
                                </p>
                                <p>
                                    <b>Razón Social:</b>
                                    {{$store.state.ventas.venta.cliente.razonsocial}}
                                </p>
                                <p>
                                    <b>Condición Frente al IVA:</b>
                                    {{$store.state.ventas.venta.cliente.condicioniva}}
                                </p>
                                <p>
                                    <b>Domicilio:</b>
                                    {{$store.state.ventas.venta.cliente.direccion}}
                                </p>
                                <p>
                                    <b>Condición de Venta:</b>
                                    {{$store.state.ventas.venta.factura.condicionventa}}
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
                                                <th>Cantidad Litros</th>
                                                <th class="hidden-sm-and-down">U. Medida</th>
                                                <th>Precio Unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle, index) in $store.state.ventas.venta.detalles"
                                                :key="index"
                                            >
                                                <th
                                                    class="hidden-sm-and-down"
                                                >{{detalle.codarticulo}}</th>
                                                <th>{{detalle.articulo}}</th>
                                                <th>{{detalle.cantidad}}</th>
                                                <th>{{detalle.cantidadLitros}}</th>
                                                <th class="hidden-sm-and-down">{{detalle.medida}}</th>
                                                <th>{{detalle.preciounitario}}</th>
                                                <th>{{detalle.subtotal}}</th>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12" class="venta-footer">
                                <div v-if="$store.state.ventas.venta.factura.observaciones">
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>{{ $store.state.ventas.venta.factura.observaciones }}</p>
                                </div>

                                <div v-if="$store.state.ventas.venta.factura.cotizacion">
                                    <v-divider></v-divider>
                                    <br />
                                    <p>
                                        <b>Cotización:</b>
                                        $ {{$store.state.ventas.venta.factura.cotizacion}}
                                        <b>Fecha:</b>
                                        {{$store.state.ventas.venta.factura.fechaCotizacion}}
                                    </p>
                                    <p>
                                        <b>Subtotal en Pesos:</b>
                                        $ {{$store.state.ventas.venta.factura.subtotalPesos}}
                                    </p>
                                    <p>
                                        <b>Total en Pesos:</b>
                                        $ {{$store.state.ventas.venta.factura.totalPesos}}
                                    </p>
                                </div>

                                <v-divider></v-divider>
                                <br />
                                <div class="footer-final">
                                    <p>
                                        <b>Subtotal:</b>
                                        U$D {{$store.state.ventas.venta.factura.subtotal}}
                                    </p>
                                    <p>
                                        <b>Bonificación:</b>
                                        {{$store.state.ventas.venta.factura.bonificacion}}%
                                    </p>
                                    <p>
                                        <b>Recargo:</b>
                                        {{$store.state.ventas.venta.factura.recargo}}%
                                    </p>
                                    <p>
                                        <b>Total:</b>
                                        U$D {{$store.state.ventas.venta.factura.total}}
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
            inProcess: false
        };
    },

    props: ["id"],

    mounted() {
        this.getVenta();
    },

    methods: {
        getVenta: async function() {
            this.inProcess = true;
            await this.$store.dispatch("ventas/show", {
                id: this.id
            });
            console.log(this.$store.state.ventas.venta);
            this.inProcess = false;
        }
    }
};
</script>

<style lang="scss">
.header-left {
    p {
        font-size: 12px;
        margin-left: 20px;
        margin-top: -12px;
        line-height: 16px;
    }
    h2 {
        margin-bottom: 40px;
    }
}

.header-right {
    border-left: 1px solid #e0e0e0;
    p {
        font-size: 12px;
        margin-right: 20px;
        margin-left: 20px;
        margin-top: -12px;
        line-height: 16px;
    }
}

@media (min-width: 601px) {
    .header-right {
        p {
            text-align: right;
        }
    }
}

.header-right {
    h2 {
        margin-bottom: 20px;
    }
}

.pre-body {
    p {
        line-height: 8px;
        margin-left: 20px;
    }
}

.detail-table {
    .v-data-table__wrapper {
        table,
        th,
        td {
            border: 1px solid #e0e0e0;
        }

        thead,
        tbody {
            th,
            td {
                text-align: center;
            }
        }
    }
}

.venta-footer {
    position: absolute;
    bottom: 0;
    line-height: 8px;
    margin-left: 20px;
    .footer-final {
        text-align: right;
        margin-right: 20px;
    }
}
</style>