<template>
    <div>
        <v-card shaped outlined :loading="inProcess" class="pb-4">
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="auto" align-self="center">Nueva factura</v-col>
                    <v-spacer></v-spacer>
                    <v-col cols="auto">
                        <v-list-item two-line class="text-right">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <b v-if="$vuetify.breakpoint.xsOnly"
                                        >N°:&nbsp;</b
                                    >
                                    <b v-else>Comprobante N°:&nbsp;</b>
                                    {{ NumComprobante }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    <b v-if="$vuetify.breakpoint.xsOnly"
                                        >P:&nbsp;</b
                                    >
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
                        :rules="[() => validateStep(1, 'facturasClienteForm')]"
                        >Cliente y condición de compra.</v-stepper-step
                    >
                    <v-stepper-content step="1">
                        <v-form ref="facturasClienteForm">
                            <v-row justify="space-around" class="my-1">
                                <!-- CLIENTE -->
                                <v-col cols="9" class="py-0">
                                    <v-text-field
                                        v-model="searchCliente"
                                        :rules="[rules.required]"
                                        @keyup="searchClienteAfter()"
                                        class="search-client-input"
                                        append-icon="fas fa-search"
                                        label="Cliente"
                                        outlined
                                    ></v-text-field>
                                    <v-card
                                        outlined
                                        class="search-client-table mb-5"
                                        v-if="searchClienteTable"
                                    >
                                        <v-row
                                            justify="center"
                                            v-if="searchInProcess"
                                            class="py-5"
                                        >
                                            <v-progress-circular
                                                :size="70"
                                                :width="7"
                                                color="primary"
                                                indeterminate
                                            ></v-progress-circular>
                                        </v-row>
                                        <div
                                            v-else-if="
                                                searchCliente != null &&
                                                    searchCliente != ''
                                            "
                                        >
                                            <v-simple-table
                                                v-if="clientes.length > 0"
                                            >
                                                <thead>
                                                    <tr>
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            Apellido Nombre
                                                        </th>
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            Documento
                                                        </th>
                                                        <th>Tipo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="(cliente,
                                                        index) in clientes"
                                                        :key="index"
                                                        class="search-client-select"
                                                        @click="
                                                            selectCliente(
                                                                cliente
                                                            )
                                                        "
                                                    >
                                                        <td>
                                                            {{
                                                                cliente.razonsocial
                                                            }}
                                                        </td>
                                                        <td>
                                                            {{
                                                                cliente.documentounico
                                                            }}
                                                        </td>
                                                        <td>
                                                            <div
                                                                v-if="
                                                                    cliente.distribuidor
                                                                "
                                                            >
                                                                Distribuidor
                                                            </div>
                                                            <div v-else>
                                                                Cliente
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </v-simple-table>
                                            <div v-else class="py-5">
                                                <h3 class="text-center">
                                                    Ningun dato coincide con lel
                                                    criterio de busqueda
                                                </h3>
                                            </div>
                                        </div>
                                    </v-card>
                                </v-col>
                                <!-- FECHA -->
                                <v-col cols="12" sm="3" class="py-0">
                                    <v-dialog
                                        ref="dialogFecha"
                                        v-model="fechaDialog"
                                        :return-value.sync="
                                            $store.state.facturas.form.fecha
                                        "
                                        persistent
                                        :width="
                                            $vuetify.breakpoint.xsOnly
                                                ? '100%'
                                                : '300px'
                                        "
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                :value="
                                                    $store.state.facturas.form
                                                        .fecha | formatDate
                                                "
                                                @input="
                                                    value =>
                                                        (store.state.facturas.form.fecha = value)
                                                "
                                                label="Fecha"
                                                :rules="[rules.required]"
                                                readonly
                                                outlined
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="
                                                $store.state.facturas.form.fecha
                                            "
                                            scrollable
                                            locale="es"
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="
                                                    $refs.dialogFecha.save(
                                                        $store.state.facturas
                                                            .form.fecha
                                                    )
                                                "
                                                >Aceptar</v-btn
                                            >
                                        </v-date-picker>
                                    </v-dialog>
                                </v-col>

                                <!-- CONDICION VENTA -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-text-field
                                        v-model="
                                            $store.state.facturas.form
                                                .condicionventa
                                        "
                                        :rules="[rules.required]"
                                        label="Condición"
                                        required
                                        outlined
                                        disabled
                                    ></v-text-field>
                                </v-col>
                                <!-- COMPROBANTE ADHERIDO -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-text-field
                                        v-model="
                                            $store.state.facturas.form
                                                .comprobanteadherido
                                        "
                                        :rules="[rules.required]"
                                        label="Factura Adherida Nº"
                                        outlined
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
                                >Continuar</v-btn
                            >
                        </v-row>
                    </v-stepper-content>

                    <!-- DETALLES -->
                    <v-stepper-step
                        :complete="step > 2"
                        step="2"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        >Detalles.</v-stepper-step
                    >
                    <v-stepper-content step="2">
                        <v-row justify="space-around" class="my-1">
                            <!-- TABLA DETALLES -->
                            <v-col cols="12" class="py-0 mb-5">
                                <v-card outlined>
                                    <v-simple-table :key="detailTableKey">
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th class="text-left">
                                                        Articulo
                                                    </th>
                                                    <th
                                                        class="text-left hidden-sm-and-down"
                                                    >
                                                        Precio
                                                    </th>
                                                    <th class="text-left">
                                                        Unidades
                                                    </th>
                                                    <th
                                                        class="text-left hidden-sm-and-down"
                                                    >
                                                        Cantidad en litros
                                                    </th>
                                                    <th
                                                        class="text-left hidden-sm-and-down"
                                                    >
                                                        Presentación
                                                    </th>
                                                    <th
                                                        class="text-left hidden-sm-and-down"
                                                    >
                                                        Bonificacion
                                                    </th>
                                                    <th
                                                        class="text-left hidden-sm-and-down"
                                                    >
                                                        Recargo
                                                    </th>
                                                    <th class="text-left">
                                                        Subtotal
                                                    </th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(detalle,
                                                    index) in detalles"
                                                    :key="index"
                                                >
                                                    <td>
                                                        {{ detalle.articulo }}
                                                    </td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >
                                                        {{ detalle.articulo }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            detalle.facturando ||
                                                                detalle.cantidad -
                                                                    detalle.cantidadfacturado
                                                        }}
                                                    </td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >
                                                        {{
                                                            detalle.cantidadLitros
                                                        }}
                                                    </td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >
                                                        {{
                                                            detalle.cantidadLitros /
                                                                detalle.cantidad
                                                        }}
                                                    </td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >
                                                        {{
                                                            detalle.bonificacion ||
                                                                0
                                                        }}
                                                    </td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >
                                                        {{
                                                            detalle.recargo || 0
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{ detalle.subtotal }}
                                                    </td>
                                                    <td class="px-0">
                                                        <v-btn
                                                            icon
                                                            color="secondary"
                                                            @click="
                                                                openEditDetailDialog(
                                                                    detalle
                                                                )
                                                            "
                                                        >
                                                            <v-icon
                                                                size="medium"
                                                                >fas
                                                                fa-pen</v-icon
                                                            >
                                                        </v-btn>
                                                    </td>
                                                    <td class="px-0">
                                                        <v-btn
                                                            icon
                                                            color="secondary"
                                                            @click="
                                                                deleteDetail(
                                                                    detalle
                                                                )
                                                            "
                                                        >
                                                            <v-icon
                                                                size="medium"
                                                                >fas
                                                                fa-times</v-icon
                                                            >
                                                        </v-btn>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row justify="center">
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mb-2"
                                @click="step = 3"
                                >Continuar</v-btn
                            >
                        </v-row>
                    </v-stepper-content>

                    <!-- BONIFICACION, RECARGO, SUBTOTAL, TOTAL -->
                    <v-stepper-step
                        :complete="step > 3"
                        step="3"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        :rules="[() => validateStep(3, 'facturasTotalesForm')]"
                        >Bonificación y recargo.</v-stepper-step
                    >
                    <v-stepper-content step="3">
                        <v-form ref="facturasTotalesForm">
                            <v-row justify="space-around" class="my-1">
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="cotizacion"
                                            :rules="[rules.required]"
                                            label="Cotizacion"
                                            outlined
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                    <!-- BONIFICACION -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.facturas.form
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
                                                $store.state.facturas.form
                                                    .recargo
                                            "
                                            type="number"
                                            label="Recargo %"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" class="py-0">
                                        <v-select
                                            v-model="
                                                $store.state.facturas.form
                                                    .tipoiva
                                            "
                                            @change="valorAgregadoControl()"
                                            :items="[21, 10.5]"
                                            :rules="[rules.required]"
                                            label="Condición frente al IVA"
                                            required
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <!-- TIPO DE COMPROBANTE -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.facturas.form
                                                    .tipocomprobante
                                            "
                                            label="Tipo de comprobante"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                </v-col>
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <v-col cols="12" class="py-0">
                                        <v-dialog
                                            ref="dialogCotizacion"
                                            v-model="dialogCotizacion"
                                            :return-value.sync="fechaCotizacion"
                                            persistent
                                            :width="
                                                $vuetify.breakpoint.xsOnly
                                                    ? '100%'
                                                    : '300px'
                                            "
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    :value="
                                                        fechaCotizacion
                                                            | formatDate
                                                    "
                                                    @input="
                                                        value =>
                                                            (fechaCotizacion = value)
                                                    "
                                                    label="Fecha de la cotización"
                                                    readonly
                                                    outlined
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="fechaCotizacion"
                                                scrollable
                                                locale="es"
                                            >
                                                <v-spacer></v-spacer>
                                                <v-btn
                                                    text
                                                    color="primary"
                                                    @click="
                                                        $refs.dialogCotizacion.save(
                                                            fechaCotizacion
                                                        )
                                                    "
                                                    >Aceptar</v-btn
                                                >
                                            </v-date-picker>
                                        </v-dialog>
                                    </v-col>
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
                                    <!-- IVA -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="valorAgregado"
                                            :label="
                                                `IVA ${$store.state.facturas
                                                    .form.tipoiva || 21}%`
                                            "
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
                                    <v-col cols="12" class="py-0">
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
                                >Continuar</v-btn
                            >
                        </v-row>
                    </v-stepper-content>

                    <!-- OBSERVACIONES, SAVE SLOT -->
                    <v-stepper-step
                        :complete="step > 4"
                        step="4"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        >Observaciones</v-stepper-step
                    >
                    <v-stepper-content step="4">
                        <v-row justify="center" class="my-1">
                            <v-col cols="12" class="py-0">
                                <v-textarea
                                    v-model="
                                        $store.state.facturas.form.observaciones
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

        <v-dialog v-model="editDetailDialog" width="500">
            <v-card>
                <v-card-title>Editar detalle</v-card-title>
                <v-divider></v-divider>
                <br />
                <v-card-text>
                    <v-form @submit.prevent="editDetail">
                        <v-row justify="center">
                            <v-col cols="12" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.articulo"
                                    label="Articulo"
                                    outlined
                                    disabled
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.cantidad"
                                    label="Unidades"
                                    outlined
                                    :rules="[
                                        rules.required,
                                        rules.facturacionMaxima
                                    ]"
                                    @keyup="editDetailControl()"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.bonificacion"
                                    label="Bonificación"
                                    outlined
                                    @keyup="editDetailControl()"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.recargo"
                                    label="Recargo"
                                    outlined
                                    @keyup="editDetailControl()"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.subtotal"
                                    label="Subtotal"
                                    outlined
                                    disabled
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <br />
                        <v-row justify="end">
                            <v-btn
                                text
                                color="error"
                                @click="editDetailDialog = false"
                                >Cancelar</v-btn
                            >
                            <div class="mx-2"></div>
                            <v-btn
                                text
                                class="elevation-0"
                                type="submit"
                                color="secondary"
                                >Guardar</v-btn
                            >
                            <div class="mx-2"></div>
                        </v-row>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from "moment";
import ClickOutside from "v-click-outside";
var facturacionMaxima = 999999999;

export default {
    directives: {
        clickOutside: ClickOutside.directive
    },

    data: () => ({
        step: 1,
        // GENERAL
        inProcess: false,
        searchInProcess: false,
        disabled: {
            detalles: true
        },
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            facturacionMaxima: value =>
                value <= Number(facturacionMaxima) ||
                "No se puede facturar más unidades"
        },
        // HEADER
        PuntoVenta: null,
        NumComprobante: null,
        // CLIENTES
        searchCliente: null,
        searchClienteTable: false,
        clientes: [],
        // DETALLES
        detalles: [],
        detailTableKey: 1,
        selectedDetail: {},
        editDetailDialog: false,
        // COTIZACION
        valorAgregado: null,
        cotizacion: 1,
        fechaCotizacion: "",
        dialogCotizacion: false,
        // SUBTOTAL
        subtotal: null,
        fechaDialog: false
    }),

    computed: {
        // Totales
        total: {
            set() {},
            get() {
                if (this.subtotal) {
                    let total =
                        Number(this.subtotal) + Number(this.valorAgregado);
                    let bonificacion = 0;
                    let recargo = 0;

                    if (this.$store.state.facturas.form.bonificacion) {
                        bonificacion =
                            Number(
                                this.$store.state.facturas.form.bonificacion *
                                    total
                            ) / 100;
                    }

                    if (this.$store.state.facturas.form.recargo) {
                        recargo =
                            Number(
                                this.$store.state.facturas.form.recargo * total
                            ) / 100;
                    }

                    return Number(total - bonificacion + recargo).toFixed(2);
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
                }
            }
        }
    },

    created() {
        if (this.$store.state.facturas.form.detalles) {
            this.detalles = this.$store.state.facturas.form.detalles;
        } else {
            this.$router.push("/remitos");
        }
    },

    mounted: async function() {
        this.inProcess = true;
        await this.checkCurrency();
        await this.getPoint();
        await this.subtotalControl();
        await this.initState();
        this.inProcess = false;
    },

    methods: {
        // GENERAL
        initState() {
            this.$store.state.facturas.form.tipocomprobante = "FACTURA";
            this.$store.state.facturas.form.condicionventa = "CONTADO";
            this.$store.state.facturas.form.tipoiva = 21;
            this.valorAgregadoControl();
        },

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

        checkCurrency() {
            return new Promise((resolve, reject) => {
                axios
                    .get("/api/consultar")
                    .then(response => {
                        this.cotizacion = response.data.valor;
                        this.fechaCotizacion = response.data.fecha;
                        resolve(response.data);
                    })
                    .catch(error => {
                        this.cotizacion = 1;
                        this.fechaCotizacion = moment().format("DD/MM/YYYY");
                        this.inProcess = false;
                        reject(error);
                    });
            });
        },
        setCurrency: async function() {
            await axios
                .post("/api/setCotizacion", {
                    cotizacion: this.cotizacion,
                    fechaCotizacion: this.fechaCotizacion
                })
                .then(response => {
                    console.log(response.data);
                })
                .catch(error => {
                    console.log(error);
                });
        },

        // HEADER
        getPoint: async function() {
            let data;
            await axios.get("/api/config").then(response => {
                data = response.data;
            });
            this.PuntoVenta = data.puntoventa;
            let response = await this.$store.dispatch("facturas/index");
            if (response.ultima) {
                this.NumComprobante = Number(response.ultima.numfactura) + 1;
            } else {
                this.NumComprobante = data.numfactura;
            }
        },

        // CLIENTES
        searchClienteAfter() {
            this.searchInProcess = true;
            this.searchClienteTable = true;
            if (this.searchCliente != null && this.searchCliente != "") {
                if (this.searchCliente == "0") {
                    this.searchCliente = "CONSUMIDOR FINAL";
                    this.$store.state.facturas.form.cliente_id = 1;
                    this.clientes = [];
                    this.searchClienteTable = false;
                    this.searchInProcess = false;
                } else {
                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }
                    this.timer = setTimeout(() => {
                        this.findCliente();
                    }, 1000);
                }
            }
        },

        findCliente: async function() {
            this.$store.state.facturas.form.cliente_id = null;
            this.clientes = [];
            axios
                .post("/api/buscando", { buscar: this.searchCliente })
                .then(response => {
                    let responseClientes = response.data.clientes;
                    let responseDistribuidores = response.data.distribuidores;
                    for (let i = 0; i < responseClientes.length; i++) {
                        this.clientes.push(responseClientes[i]);
                    }
                    for (let i = 0; i < responseDistribuidores.length; i++) {
                        this.clientes.push(responseDistribuidores[i]);
                    }
                    this.searchInProcess = false;
                })
                .catch(error => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        selectCliente(cliente) {
            this.searchCliente = cliente.razonsocial;
            this.$store.state.facturas.form.cliente_id = cliente.id;
            this.clientes = [];
            this.searchClienteTable = false;
        },

        // DETALLES
        openEditDetailDialog(detail) {
            facturacionMaxima = detail.cantidad - detail.cantidadfacturado;
            let presentacion = detail.cantidadLitros / detail.cantidad;
            this.selectedDetail = Object.assign({}, detail);
            this.selectedDetail.presentacion = presentacion;
            this.editDetailDialog = true;
        },

        editDetailControl() {
            this.selectedDetail.cantidadLitros =
                this.selectedDetail.cantidad * this.selectedDetail.presentacion;

            let sub =
                Number(this.selectedDetail.preciounitario) *
                Number(this.selectedDetail.cantidadLitros);

            let bonificacion = this.selectedDetail.bonificacion
                ? Number(this.selectedDetail.bonificacion * sub) / 100
                : 0;

            let recargo = this.selectedDetail.recargo
                ? Number(this.selectedDetail.recargo * sub) / 100
                : 0;

            let subTotal = Number(sub - bonificacion + recargo).toFixed(2);

            this.selectedDetail.subtotal = subTotal;

            this.selectedDetail.subtotalPesos = Number(
                subTotal * Number(this.selectedDetail.cotizacion)
            ).toFixed(2);
        },

        async editDetail() {
            let index = this.detalles.indexOf(
                this.detalles.find(
                    element => element.id == this.selectedDetail.id
                )
            );

            this.detalles[index] = this.selectedDetail;
            this.detalles[index].fancturando = this.selectedDetail.cantidad;
            this.selectedDetail = {};
            this.editDetailDialog = false;
            this.detailTableKey += 1;
            await this.subtotalControl();
            await this.valorAgregadoControl();
        },

        async deleteDetail(detalle) {
            let index = this.detalles.indexOf(detalle);
            this.detalles.splice(index, 1);
            await this.subtotalControl();
            await this.valorAgregadoControl();
        },

        // SUBTOTAL
        valorAgregadoControl() {
            if (this.subtotal) {
                let sub = Number(this.subtotal);
                let iva = Number(this.$store.state.facturas.form.tipoiva);

                this.valorAgregado = (sub * iva) / 100;
            } else {
                this.valorAgregado = null;
            }
        },

        subtotalControl() {
            if (this.detalles.length > 0) {
                let sub = 0;
                for (let i = 0; i < this.detalles.length; i++) {
                    sub += this.detalles[i].subtotal * 1;
                }
                this.subtotal = Number(sub).toFixed(2);
            }
        },

        // FORM
        setData: async function() {
            await this.setCurrency();
            if (this.$refs.facturasClienteForm.validate()) {
                this.$store.state.facturas.form.subtotal = this.subtotal;
                this.$store.state.facturas.form.valorAgregado = this.valorAgregado;
                this.$store.state.facturas.form.total = this.total;
                this.$store.state.facturas.form.totalPesos = this.totalPesos;
                this.$store.state.facturas.form.cotizacion = this.cotizacion;
                this.$store.state.facturas.form.fechaCotizacion = this.fechaCotizacion;
                this.$store.state.facturas.form.detalles = this.detalles;
                this.$store.state.facturas.form.numfactura = this.NumComprobante;
                return true;
            }
        }
    }
};
</script>

<style lang="scss">
.btn-td {
    cursor: pointer;
}
</style>
