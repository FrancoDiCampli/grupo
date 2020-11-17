<template>
    <div>
        <div v-if="$store.state.pedidos.pedido" :loading="$store.state.inProcess">
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123">
                    <v-card-text class="pa-0 black--text">
                        <v-menu offset-y>
                            <template v-slot:activator="{ on }">
                                <div class="option-button" v-on="on">
                                    <v-icon>fas fa-ellipsis-v</v-icon>
                                </div>
                            </template>
                            <v-list>
                                <v-list-item @click="print()">
                                    <v-list-item-title>Imprimir</v-list-item-title>
                                </v-list-item>
                                <v-list-item
                                    v-if="$store.state.pedidos.pedido.pedido.numventa == null"
                                    :to="`/pedidos/editar/${id}`"
                                >
                                    <v-list-item-title>Editar</v-list-item-title>
                                </v-list-item>
                                <v-list-item
                                    v-if="$store.state.pedidos.pedido.pedido.numventa == null"
                                    @click="preventDialog = true"
                                >
                                    <v-list-item-title>Generar venta</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <v-row>
                            <v-col cols="12">
                                <h2 class="text-center mb-3">NOTA DE PEDIDO</h2>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-left">
                                <h2 class="text-center">
                                    {{
                                    $store.state.pedidos.pedido
                                    .configuracion.nombrefantasia
                                    }}
                                </h2>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.pedidos.pedido
                                    .configuracion.razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio comercial:</b>
                                    {{
                                    $store.state.pedidos.pedido
                                    .configuracion.domiciliocomercial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.pedidos.pedido
                                    .configuracion.condicioniva
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">NOTA DE PEDIDO</h2>
                                <p>
                                    <b>Punto de venta:</b>
                                    0000{{
                                    $store.state.pedidos.pedido
                                    .configuracion.puntoventa
                                    }}
                                </p>
                                <p>
                                    <b>Comprobante adherido</b>
                                    {{
                                    $store.state.pedidos.pedido.pedido
                                    .numpresupuesto
                                    }}
                                </p>
                                <p>
                                    <b>Fecha de emisión:</b>
                                    {{
                                    $store.state.pedidos.pedido.pedido.fecha
                                    }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{
                                    $store.state.pedidos.pedido
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Ingresos brutos:</b>
                                    {{
                                    $store.state.pedidos.pedido
                                    .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Inicio de actividades:</b>
                                    {{
                                    $store.state.pedidos.pedido
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
                                    $store.state.pedidos.pedido.cliente
                                    .documentounico
                                    }}
                                </p>
                                <p>
                                    <b>Razón social:</b>
                                    {{
                                    $store.state.pedidos.pedido.cliente
                                    .razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Condición frente al IVA:</b>
                                    {{
                                    $store.state.pedidos.pedido.cliente
                                    .condicioniva
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio:</b>
                                    {{
                                    $store.state.pedidos.pedido.cliente
                                    .direccion
                                    }}
                                </p>
                                <p>
                                    <b>Vencimiento:</b>
                                    {{
                                    $store.state.pedidos.pedido.pedido
                                    .vencimiento
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
                                                <th class="hidden-sm-and-down">U. medida</th>
                                                <th>Precio unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle,
                                                index) in $store.state.pedidos
                                                    .pedido.detalles"
                                                :key="index"
                                            >
                                                <th
                                                    class="hidden-sm-and-down"
                                                >{{ detalle.codarticulo }}</th>
                                                <th>{{ detalle.articulo }}</th>
                                                <th>{{ detalle.cantidad }}</th>
                                                <th>{{ detalle.cantidadLitros }}</th>
                                                <th class="hidden-sm-and-down">{{ detalle.medida }}</th>
                                                <th>{{ detalle.preciounitario }}</th>
                                                <th>{{ detalle.subtotal }}</th>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12" class="comprobantes-footer">
                                <div
                                    v-if="
                                        $store.state.pedidos.pedido.pedido
                                            .observaciones
                                    "
                                >
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>
                                        {{
                                        $store.state.pedidos.pedido.pedido
                                        .observaciones
                                        }}
                                    </p>
                                </div>

                                <v-divider></v-divider>
                                <br />
                                <div class="footer-final">
                                    <p>
                                        <b>Subtotal:</b>
                                        U$D
                                        {{
                                        $store.state.pedidos.pedido.pedido
                                        .subtotal
                                        }}
                                    </p>
                                    <p>
                                        <b>Bonificación:</b>
                                        {{
                                        $store.state.pedidos.pedido.pedido
                                        .bonificacion
                                        }}%
                                    </p>
                                    <p>
                                        <b>Recargo:</b>
                                        {{
                                        $store.state.pedidos.pedido.pedido
                                        .recargo
                                        }}%
                                    </p>
                                    <p>
                                        <b>Total:</b>
                                        U$D
                                        {{
                                        $store.state.pedidos.pedido.pedido
                                        .total
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

        <v-dialog v-model="preventDialog" persistent width="400px">
            <v-card v-if="inProcess">
                <v-row justify="center">
                    <v-progress-circular
                        :size="70"
                        :width="7"
                        color="primary"
                        indeterminate
                        style="margin: 32px 0 32px 0;"
                    ></v-progress-circular>
                </v-row>
            </v-card>
            <v-card v-else>
                <v-card-title class="headline">¿Estas seguro?</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12">
                            Se generará un remito a partir de la nota de pedido
                            seleccionada
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                outlined
                                label="Remito adherido N°"
                                v-model="remitoadherido"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="preventDialog = false"
                        :disabled="inProcess"
                    >Cancelar</v-btn>
                    <v-btn color="success" text @click="sold()" :disabled="inProcess">Aceptar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            preventDialog: false,
            remitoadherido: null,
            inProcess: false,
        };
    },

    props: ["id"],

    mounted() {
        this.getPedido();
    },

    methods: {
        getPedido: async function () {
            this.inProcess = true;
            await this.$store.dispatch("pedidos/show", {
                id: this.id,
            });
            this.inProcess = false;
        },

        print() {
            let id = this.$store.state.pedidos.pedido.pedido.id;
            this.$store.dispatch("PDF/printPedido", { id: id });
        },

        async sold() {
            this.inProcess = true;
            await this.$store.dispatch("pedidos/vender", {
                id: this.id,
                remitoadherido: this.remitoadherido,
            });
            await this.getPedido();
            this.preventDialog = false;
            this.inProcess = false;
            this.remitoadherido = null;
        },
    },
};
</script>

<style lang="scss"></style>
