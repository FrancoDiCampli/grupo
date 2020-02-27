<template>
    <div>
        <v-card shaped outlined :loading="inProcess" class="pb-4">
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="auto" align-self="center">Nueva Venta</v-col>
                    <v-spacer></v-spacer>
                    <v-col cols="auto">
                        <v-list-item two-line class="text-right">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <b v-if="$vuetify.breakpoint.xsOnly">N°:&nbsp;</b>
                                    <b v-else>Comprobante N°:&nbsp;</b>
                                    {{ NumComprobante }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    <b v-if="$vuetify.breakpoint.xsOnly">P:&nbsp;</b>
                                    <b v-else>Punto de venta:&nbsp;</b>
                                    {{ PuntoVenta }}
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-col>
                </v-row>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-0 ma-0">
                <v-stepper v-model="step" vertical class="elevation-0">
                    <!-- CLIENTE, CONDICION, COMPROBANTE -->
                    <v-stepper-step
                        :complete="step > 1"
                        step="1"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        :rules="[() => validateStep(1, 'ventasClienteForm')]"
                    >Cliente y condición de compra.</v-stepper-step>
                    <v-stepper-content step="1">
                        <v-form ref="ventasClienteForm">
                            <v-row justify="space-around" class="my-1">
                                <Clientes></Clientes>
                                <!-- <v-col cols="12" sm="6" class="py-0">
                                    <v-select
                                        outlined
                                        :items="['Efectivo', 'Tarjeta', 'Cheque', 'Transferencia']"
                                        label="Tipo de pago"
                                    ></v-select>
                                </v-col>-->
                                <!-- CONDICION VENTA -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-select
                                        v-model="condicion"
                                        :items="condiciones"
                                        :rules="[rules.required]"
                                        label="Condición"
                                        required
                                        outlined
                                    ></v-select>
                                </v-col>
                                <!-- COMPROBANTE ADHERIDO -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-text-field
                                        v-model="
                                            $store.state.ventas.form
                                                .comprobanteadherido
                                        "
                                        label="Comprobante Adherido Nº"
                                        outlined
                                        type="number"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-form>
                        <v-row justify="center">
                            <v-btn
                                tile
                                @click="step = 2"
                                color="secondary"
                                class="elevation-0 mb-2"
                            >Continuar</v-btn>
                        </v-row>
                    </v-stepper-content>

                    <!-- ARTICULOS, DETALLES -->
                    <v-stepper-step
                        :complete="step > 2"
                        step="2"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        :rules="[() => validateDetail(2)]"
                    >
                        Detalles.
                        <small v-if="!validateDetail(2)">Debe ingresar al menos un detalle.</small>
                    </v-stepper-step>
                    <v-stepper-content step="2">
                        <Detalles ref="detalles" :maximum="true"></Detalles>
                        <v-row justify="center">
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mb-2"
                                @click="nextStep('detalles', 3)"
                            >Continuar</v-btn>
                        </v-row>
                    </v-stepper-content>

                    <!-- BONIFICACION, RECARGO, SUBTOTAL, TOTAL -->
                    <v-stepper-step
                        :complete="step > 3"
                        step="3"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        :rules="[() => validateStep(3, 'ventasTotalesForm')]"
                    >Bonificación y recargo.</v-stepper-step>
                    <v-stepper-content step="3">
                        <v-form ref="ventasTotalesForm">
                            <v-row justify="space-around" class="my-1">
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <!-- BONIFICACION -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.ventas.form
                                                    .bonificacion
                                            "
                                            type="number"
                                            label="Bonificacion %"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                    <!-- RECARGO -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.ventas.form.recargo
                                            "
                                            type="number"
                                            label="Recargo %"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                    <!-- TIPO DE COMPROBANTE -->
                                    <v-col cols="12" class="py-0">
                                        <v-select
                                            v-model="tipo"
                                            :items="tipos"
                                            :rules="[rules.required]"
                                            label="Tipo de comprobante"
                                            outlined
                                        ></v-select>
                                    </v-col>
                                </v-col>
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <!-- SUBTOTAL -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="subtotal"
                                            type="number"
                                            label="Subtotal"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                    <!-- TOTAL -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="total"
                                            type="number"
                                            label="Total"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                    <!-- TOTAL PESOS -->
                                    <v-col cols="12" class="py-0" v-if="condicion == 'CONTADO'">
                                        <v-text-field
                                            v-model="totalPesos"
                                            label="Total en pesos"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                </v-col>
                            </v-row>
                        </v-form>
                        <v-row justify="center">
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mb-2"
                                @click="step = 4"
                            >Continuar</v-btn>
                        </v-row>
                    </v-stepper-content>

                    <!-- OBSERVACIONES, SAVE SLOT -->
                    <v-stepper-step
                        :complete="step > 4"
                        step="4"
                        :editable="true"
                        edit-icon="fas fa-pen"
                    >Observaciones</v-stepper-step>
                    <v-stepper-content step="4">
                        <v-row justify="center" class="my-1">
                            <v-col cols="12" class="py-0">
                                <v-textarea
                                    v-model="
                                        $store.state.ventas.form.observaciones
                                    "
                                    outlined
                                    label="Observaciones"
                                    no-resize
                                ></v-textarea>
                            </v-col>
                            <slot></slot>
                        </v-row>
                    </v-stepper-content>
                </v-stepper>
            </v-card-text>
        </v-card>

        <v-dialog v-model="detallesDialog" width="500" persistent>
            <v-card>
                <v-card-title primary-title>¡Ha ocurrido un error!</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <br />Debe ingresar al menos un detalle.
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                    <br />
                    <v-row justify="end">
                        <v-btn
                            tile
                            @click="detallesDialog = false"
                            :disabled="$store.state.inProcess"
                            color="error"
                            class="mx-2 elevation-0"
                        >Cerrar</v-btn>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-model="condicionDialog" width="500" persistent>
            <v-card>
                <v-card-title primary-title>¡Ha ocurrido un error!</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <br />La condición de venta a un consumidor final debe ser
                    contado.
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                    <br />
                    <v-row justify="end">
                        <v-btn
                            tile
                            @click="condicionDialog = false"
                            :disabled="$store.state.inProcess"
                            color="error"
                            class="mx-2 elevation-0"
                        >Cerrar</v-btn>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from "moment";

import Clientes from "../formularios/comprobantes/Clientes";
import Detalles from "../formularios/comprobantes/Detalles";

export default {
    data: () => ({
        step: 1,
        // GENERAL
        inProcess: false,
        rules: {
            required: value => !!value || "Este campo es obligatorio"
        },
        // HEADER
        PuntoVenta: null,
        NumComprobante: null,
        // CONDICION
        condicion: null,
        condiciones: ["CONTADO", "CUENTA CORRIENTE"],
        // DETALLES
        detalles: [],
        // SUBTOTAL
        subtotal: null,
        // COMPROBANTES
        tipo: null,
        tipos: ["REMITO X", "NOTA DE PEDIDO"],
        // MODALS
        detallesDialog: false,
        condicionDialog: false
    }),

    components: {
        Detalles,
        Clientes
    },

    watch: {
        // TIPO DE COMPROBANTE
        condicion() {
            if (this.condicion) {
                if (this.condicion == "CONTADO") {
                    this.tipo = "REMITO X";
                } else if (this.condicion == "CUENTA CORRIENTE") {
                    this.tipo = "NOTA DE PEDIDO";
                }
            }
        }
    },

    computed: {
        total: {
            set() {},
            get() {
                if (this.subtotal) {
                    let total = this.subtotal;
                    let bonificacion = 0;
                    let recargo = 0;

                    if (this.$store.state.ventas.form.bonificacion) {
                        bonificacion =
                            Number(
                                this.$store.state.ventas.form.bonificacion *
                                    this.subtotal
                            ) / 100;
                    }

                    if (this.$store.state.ventas.form.recargo) {
                        recargo =
                            Number(
                                this.$store.state.ventas.form.recargo *
                                    this.subtotal
                            ) / 100;
                    }

                    return total - bonificacion + recargo;
                } else {
                    return null;
                }
            }
        },

        totalPesos: {
            set() {},
            get() {
                if (this.total && this.cotizacion) {
                    return Number(this.total * this.cotizacion).toFixed(2);
                } else {
                    return null;
                }
            }
        }
    },

    async mounted() {
        this.inProcess = true;
        await this.getPoint();
        this.inProcess = false;
    },

    methods: {
        // GENERAL
        validateStep(n, form) {
            if (this.step > n) {
                if (this.$refs[form]) {
                    if (!this.$refs[form].validate()) {
                        return false;
                    }
                }
            }
            return true;
        },

        validateDetail(n) {
            if (this.step > n) {
                let detalles = this.$refs.detalles.getDetalles();
                if (detalles.length <= 0) {
                    return false;
                }
            }

            return true;
        },

        nextStep(form, step) {
            if (form == "detalles") {
                this.detalles = this.$refs.detalles.getDetalles();
                this.subtotal = this.$refs.detalles.getSubtotal();
            }

            this.step = 3;
        },

        // HEADER
        getPoint: async function() {
            let data;
            await axios.get("/api/config").then(response => {
                data = response.data;
            });
            this.PuntoVenta = data.puntoventa;
            let response = await this.$store.dispatch("ventas/index");
            if (response.ultima) {
                this.NumComprobante = Number(response.ultima.numventa) + 1;
            } else {
                this.NumComprobante = data.numventa;
            }
        },

        // FORM
        setData: async function() {
            if (
                this.$refs.ventasClienteForm.validate() &&
                this.$refs.ventasTotalesForm.validate()
            ) {
                if (this.detalles.length <= 0) {
                    this.detallesDialog = true;
                    return false;
                } else if (
                    this.$store.state.ventas.form.cliente_id == 1 &&
                    this.condicion == "CUENTA CORRIENTE"
                ) {
                    this.condicionDialog = true;
                    return false;
                } else {
                    this.$store.state.ventas.form.condicionventa = this.condicion;
                    this.$store.state.ventas.form.subtotal = this.subtotal;
                    this.$store.state.ventas.form.total = this.total;
                    this.$store.state.ventas.form.totalPesos = this.totalPesos;
                    this.$store.state.ventas.form.tipoComprobante = this.tipo;
                    this.$store.state.ventas.form.detalles = this.detalles;
                    this.$store.state.ventas.form.cotizacion = this.cotizacion;
                    this.$store.state.ventas.form.fechaCotizacion = this.fechaCotizacion;
                    this.$store.state.ventas.form.numventa = this.NumComprobante;
                    return true;
                }
            }
        },

        resetData: async function() {
            this.clientes = [];
            this.detalles = [];
            this.$refs.ventasClienteForm.reset();
            this.$refs.ventasTotalesForm.reset();
            this.$refs.detalles.resetForm();
            this.step = 1;
        }
    }
};
</script>

<style lang="scss"></style>
