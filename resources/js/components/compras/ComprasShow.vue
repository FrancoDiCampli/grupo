<template>
    <div>
        <div v-if="$store.state.compras.compra">
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123" :loading="$store.state.inProcess">
                    <v-card-text class="pa-0 black--text">
                        <div class="print-button" @click="print()">
                            <v-icon>fas fa-print</v-icon>
                        </div>
                        <v-row>
                            <v-col cols="12">
                                <h2 class="text-center mb-3">REMITO COMPRA</h2>
                                <v-divider></v-divider>
                            </v-col>

                            <v-col cols="12" sm="6" class="header-left">
                                <h2
                                    class="text-center"
                                >{{ $store.state.compras.compra.configuracion.nombrefantasia }}</h2>
                                <p>
                                    <b>Razón Social:</b>
                                    {{ $store.state.compras.compra.configuracion.razonsocial }}
                                </p>
                                <p>
                                    <b>Domicilio Comercial:</b>
                                    {{ $store.state.compras.compra.configuracion.domiciliocomercial }}
                                </p>
                                <p>
                                    <b>Condición Frente al IVA:</b>
                                    {{ $store.state.compras.compra.configuracion.condicioniva }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">REMITO</h2>
                                <p>
                                    <b>Punto de Venta:</b>
                                    0000{{ $store.state.compras.compra.configuracion.puntoventa }}
                                    <b>Comprobante Nº:</b>
                                    {{ $store.state.compras.compra.remito.numcompra }}
                                </p>
                                <p>
                                    <b>Fecha de Emisión:</b>
                                    {{ $store.state.compras.compra.remito.fecha }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{ $store.state.compras.compra.configuracion.cuit }}
                                </p>
                                <p>
                                    <b>Ingresos Brutos:</b>
                                    {{ $store.state.compras.compra.configuracion.cuit }}
                                </p>
                                <p>
                                    <b>Inicio de Actividades:</b>
                                    {{ $store.state.compras.compra.configuracion.inicioactividades }}
                                </p>
                            </v-col>
                            <v-col cols="12" class="pre-body">
                                <v-divider></v-divider>
                                <br />
                                <p>
                                    <b>CUIT:</b>
                                    {{$store.state.compras.compra.proveedor.cuit}}
                                </p>
                                <p>
                                    <b>Razón Social:</b>
                                    {{$store.state.compras.compra.proveedor.razonsocial}}
                                </p>
                                <p>
                                    <b>Domicilio:</b>
                                    {{$store.state.compras.compra.proveedor.direccion}}
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
                                                <th class="hidden-sm-and-down">U. Medida</th>
                                                <th>Precio Unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle, index) in $store.state.compras.compra.detalles"
                                                :key="index"
                                            >
                                                <th
                                                    class="hidden-sm-and-down"
                                                >{{detalle.codarticulo}}</th>
                                                <th>{{detalle.articulo}}</th>
                                                <th>{{detalle.cantidad}}</th>
                                                <th class="hidden-sm-and-down">{{detalle.medida}}</th>
                                                <th>{{detalle.preciounitario}}</th>
                                                <th>{{detalle.subtotal}}</th>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12" class="comprobantes-footer">
                                <div v-if="$store.state.compras.compra.remito.observaciones">
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>{{ $store.state.compras.compra.remito.observaciones }}</p>
                                </div>

                                <v-divider></v-divider>
                                <br />
                                <div class="footer-final">
                                    <p>
                                        <b>Subtotal:</b>
                                        ${{$store.state.compras.compra.remito.subtotal}}
                                    </p>
                                    <p>
                                        <b>Bonificación:</b>
                                        {{$store.state.compras.compra.remito.bonificacion}}%
                                    </p>
                                    <p>
                                        <b>Recargo:</b>
                                        {{$store.state.compras.compra.remito.recargo}}%
                                    </p>
                                    <p>
                                        <b>Total:</b>
                                        ${{$store.state.compras.compra.remito.total}}
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
        this.getCompra();
    },

    methods: {
        getCompra: async function() {
            this.inProcess = true;
            await this.$store.dispatch("compras/show", {
                id: this.id
            });
            this.inProcess = false;
        },

        print() {
            let id = this.$store.state.compras.compra.remito.id;
            this.$store.dispatch("PDF/printCompra", { id: id });
        }
    }
};
</script>

<style lang="scss">
</style>