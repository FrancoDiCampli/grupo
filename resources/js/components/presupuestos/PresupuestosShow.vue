<template>
    <div>
        <div
            v-if="$store.state.presupuestos.presupuesto"
            :loading="$store.state.inProcess"
        >
            <v-row justify="center">
                <v-card shaped outlined width="794" height="1123">
                    <v-card-text class="pa-0 black--text">
                        <div class="print-button" @click="print()">
                            <v-icon>fas fa-print</v-icon>
                        </div>
                        <v-row>
                            <v-col cols="12">
                                <h2 class="text-center mb-3">PRESUPUESTO</h2>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-left">
                                <h2 class="text-center">
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.nombrefantasia
                                    }}
                                </h2>
                                <p>
                                    <b>Razón Social:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio Comercial:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.domiciliocomercial
                                    }}
                                </p>
                                <p>
                                    <b>Condición Frente al IVA:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.condicioniva
                                    }}
                                </p>
                            </v-col>
                            <v-col cols="12" sm="6" class="header-right">
                                <h2 class="text-center">PRESUPUESTO</h2>
                                <p>
                                    <b>Punto de venta:</b>
                                    0000{{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.puntoventa
                                    }}
                                    <b>Comprobante Nº:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .presupuesto.numpresupuesto
                                    }}
                                </p>
                                <p>
                                    <b>Fecha de Emisión:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .presupuesto.fecha
                                    }}
                                </p>
                                <p>
                                    <b>Cuit:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Ingresos Brutos:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .configuracion.cuit
                                    }}
                                </p>
                                <p>
                                    <b>Inicio de Actividades:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
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
                                        $store.state.presupuestos.presupuesto
                                            .cliente.documentounico
                                    }}
                                </p>
                                <p>
                                    <b>Razón Social:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .cliente.razonsocial
                                    }}
                                </p>
                                <p>
                                    <b>Condición Frente al IVA:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .cliente.condicioniva
                                    }}
                                </p>
                                <p>
                                    <b>Domicilio:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .cliente.direccion
                                    }}
                                </p>
                                <p>
                                    <b>Vencimiento:</b>
                                    {{
                                        $store.state.presupuestos.presupuesto
                                            .presupuesto.vencimiento
                                    }}
                                </p>
                                <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12">
                                <v-simple-table class="detail-table mx-5">
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th class="hidden-sm-and-down">
                                                    Cod. Producto
                                                </th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Cantidad Litros</th>
                                                <th class="hidden-sm-and-down">
                                                    U. Medida
                                                </th>
                                                <th>Precio Unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle,
                                                index) in $store.state
                                                    .presupuestos.presupuesto
                                                    .detalles"
                                                :key="index"
                                            >
                                                <th class="hidden-sm-and-down">
                                                    {{ detalle.codarticulo }}
                                                </th>
                                                <th>{{ detalle.articulo }}</th>
                                                <th>{{ detalle.cantidad }}</th>
                                                <th>
                                                    {{ detalle.cantidadLitros }}
                                                </th>
                                                <th class="hidden-sm-and-down">
                                                    {{ detalle.medida }}
                                                </th>
                                                <th>
                                                    {{ detalle.preciounitario }}
                                                </th>
                                                <th>{{ detalle.subtotal }}</th>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12" class="comprobantes-footer">
                                <div
                                    v-if="
                                        $store.state.presupuestos.presupuesto
                                            .presupuesto.observaciones
                                    "
                                >
                                    <p>
                                        <b>Observaciones:</b>
                                    </p>
                                    <p>
                                        {{
                                            $store.state.presupuestos
                                                .presupuesto.presupuesto
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
                                            $store.state.presupuestos
                                                .presupuesto.presupuesto
                                                .subtotal
                                        }}
                                    </p>
                                    <p>
                                        <b>Bonificación:</b>
                                        {{
                                            $store.state.presupuestos
                                                .presupuesto.presupuesto
                                                .bonificacion
                                        }}%
                                    </p>
                                    <p>
                                        <b>Recargo:</b>
                                        {{
                                            $store.state.presupuestos
                                                .presupuesto.presupuesto
                                                .recargo
                                        }}%
                                    </p>
                                    <p>
                                        <b>Total:</b>
                                        U$D
                                        {{
                                            $store.state.presupuestos
                                                .presupuesto.presupuesto.total
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
                <v-progress-circular
                    :size="70"
                    :width="7"
                    color="primary"
                    indeterminate
                ></v-progress-circular>
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
        this.getPresupuesto();
    },

    methods: {
        getPresupuesto: async function() {
            this.inProcess = true;
            await this.$store.dispatch("presupuestos/show", {
                id: this.id
            });
            this.inProcess = false;
        },

        print() {
            let id = this.$store.state.presupuestos.presupuesto.presupuesto.id;
            this.$store.dispatch("PDF/printPresupuesto", { id: id });
        }
    }
};
</script>

<style lang="scss"></style>
