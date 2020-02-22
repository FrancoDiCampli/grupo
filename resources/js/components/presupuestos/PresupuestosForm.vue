<template>
    <div>
        <v-card shaped outlined :loading="inProcess" class="pb-4">
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="auto" align-self="center">Nueva Presupuesto</v-col>
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
                        :rules="[
                            () => validateStep(1, 'presupuestosClienteForm')
                        ]"
                    >Cliente y vencimiento.</v-stepper-step>
                    <v-stepper-content step="1">
                        <v-form ref="presupuestosClienteForm">
                            <v-row justify="space-around" class="my-1">
                                <!-- CLIENTE -->
                                <v-col cols="12" sm="7" class="py-0">
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
                                        <v-row justify="center" v-if="searchInProcess" class="py-5">
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
                                            <v-simple-table v-if="clientes.length > 0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-xs-left">Apellido Nombre</th>
                                                        <th class="text-xs-left">Documento</th>
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
                                <!-- VENCIMIENTO -->
                                <v-col cols="12" sm="5" class="py-0">
                                    <v-dialog
                                        ref="dialogVencimiento"
                                        v-model="vencimiento"
                                        :return-value.sync="
                                            $store.state.presupuestos.form
                                                .vencimiento
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
                                                v-model="
                                                    $store.state.presupuestos
                                                        .form.vencimiento
                                                "
                                                label="Vencimiento"
                                                readonly
                                                outlined
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="
                                                $store.state.presupuestos.form
                                                    .vencimiento
                                            "
                                            scrollable
                                            locale="es"
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="
                                                    $refs.dialogVencimiento.save(
                                                        $store.state
                                                            .presupuestos.form
                                                            .vencimiento
                                                    )
                                                "
                                            >Aceptar</v-btn>
                                        </v-date-picker>
                                    </v-dialog>
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
                        <v-row justify="space-around" class="my-1">
                            <!-- ARTICULOS -->
                            <v-col cols="12" class="py-0 articulos-panel">
                                <v-expansion-panels v-model="articulosPanel">
                                    <v-expansion-panel>
                                        <v-expansion-panel-header expand-icon="fas fa-caret-down">
                                            <div
                                                v-if="articuloSelected.articulo"
                                            >{{ articuloSelected.articulo }}</div>
                                            <div v-else>Articulos</div>
                                        </v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-simple-table v-if="articulos.length > 0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-xs-left">Codigo</th>
                                                        <th class="text-xs-left">Articulo</th>
                                                        <th class="text-xs-left">Precio</th>
                                                        <th class="text-xs-left">Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="(articulo,
                                                        index) in articulos"
                                                        :key="index"
                                                        :class="
                                                            articulo.stock > 0
                                                                ? 'search-articulo-select'
                                                                : ''
                                                        "
                                                        @click="
                                                            selectArticulo(
                                                                articulo
                                                            )
                                                        "
                                                    >
                                                        <td>
                                                            {{
                                                            articulo.codarticulo
                                                            }}
                                                        </td>
                                                        <td>
                                                            {{
                                                            articulo.articulo
                                                            }}
                                                        </td>
                                                        <td>
                                                            {{
                                                            articulo.precio
                                                            }}
                                                        </td>
                                                        <td>{{ articulo.stock }}</td>
                                                    </tr>
                                                </tbody>
                                            </v-simple-table>
                                            <div v-else class="py-5">
                                                <h3 class="text-center">
                                                    No existe ningún articulo
                                                    registrado
                                                </h3>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-col>
                            <v-form ref="detailForm">
                                <v-row justify="center" class="px-3">
                                    <!-- DETALLES -->
                                    <v-col cols="12" sm="4" class="py-0">
                                        <v-text-field
                                            v-model="articuloSelected.precio"
                                            :rules="[rules.required]"
                                            :disabled="disabled.detalles"
                                            label="Precio"
                                            required
                                            outlined
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="4" class="py-0">
                                        <v-text-field
                                            v-model="articuloSelected.cantidad"
                                            :rules="[rules.required]"
                                            :disabled="disabled.detalles"
                                            label="Unidades"
                                            required
                                            outlined
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="4" class="py-0">
                                        <v-text-field
                                            v-model="cantidadLitros"
                                            :rules="[rules.required]"
                                            label="Cantidad en litros"
                                            required
                                            outlined
                                            disabled
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                    <!-- COTIZACION -->
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="dolares"
                                            :rules="[rules.required]"
                                            label="Subtotal"
                                            outlined
                                            disabled
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="pesos"
                                            :rules="[rules.required]"
                                            label="Subtotal en Pesos"
                                            outlined
                                            disabled
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-form>
                            <v-row justify="center" class="px-3">
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-text-field
                                        v-model="cotizacion"
                                        :rules="[rules.required]"
                                        label="Cotizacion"
                                        outlined
                                        type="number"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" class="py-0">
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
                                                v-model="fechaCotizacion"
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
                                            >Aceptar</v-btn>
                                        </v-date-picker>
                                    </v-dialog>
                                </v-col>
                                <v-col cols="12">
                                    <v-row justify="center" class="mb-5">
                                        <v-btn
                                            @click="addDetail()"
                                            outlined
                                            tile
                                            color="secondary"
                                            :disabled="disabled.detalles"
                                        >Añadir detalle</v-btn>
                                    </v-row>
                                </v-col>
                            </v-row>

                            <!-- TABLA DETALLES -->
                            <v-col cols="12" class="py-0 mb-5">
                                <v-card outlined>
                                    <v-simple-table>
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th class="text-left">Articulo</th>
                                                    <th class="text-left hidden-sm-and-down">Precio</th>
                                                    <th class="text-left">Unidades</th>

                                                    <th class="text-left">Subtotal</th>
                                                    <th
                                                        class="text-left hidden-sm-and-down"
                                                    >Subtotal en pesos</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(detalle,
                                                    index) in detalles"
                                                    :key="index"
                                                >
                                                    <td>{{ detalle.articulo }}</td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >{{ detalle.precio }}</td>
                                                    <td>{{ detalle.cantidad }}</td>
                                                    <td>
                                                        {{
                                                        detalle.subtotalDolares
                                                        }}
                                                    </td>
                                                    <td class="hidden-sm-and-down">
                                                        {{
                                                        detalle.subtotalPesos
                                                        }}
                                                    </td>
                                                    <td>
                                                        <v-btn
                                                            icon
                                                            color="secondary"
                                                            @click="
                                                                deleteDetail(
                                                                    detalle
                                                                )
                                                            "
                                                        >
                                                            <v-icon size="medium">
                                                                fas
                                                                fa-times
                                                            </v-icon>
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
                            >Continuar</v-btn>
                        </v-row>
                    </v-stepper-content>

                    <!-- BONIFICACION, RECARGO, SUBTOTAL, TOTAL -->
                    <v-stepper-step
                        :complete="step > 3"
                        step="3"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        :rules="[
                            () => validateStep(3, 'presupuestosTotalesForm')
                        ]"
                    >Bonificación y recargo.</v-stepper-step>
                    <v-stepper-content step="3">
                        <v-form ref="presupuestosTotalesForm">
                            <v-row justify="space-around" class="my-1">
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <!-- BONIFICACION -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.presupuestos.form
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
                                                $store.state.presupuestos.form
                                                    .recargo
                                            "
                                            type="number"
                                            label="Recargo %"
                                            outlined
                                        ></v-text-field>
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
                                        $store.state.presupuestos.form
                                            .observaciones
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
    </div>
</template>

<script>
import moment from "moment";
var cantidadMaxima = 999999999;

export default {
    data: () => ({
        step: 1,
        // GENERAL
        inProcess: false,
        focus: null,
        searchInProcess: false,
        disabled: {
            detalles: true
        },
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            cantidadMaxima: value =>
                value <= Number(cantidadMaxima) ||
                "La cantidad no puede superar el stock existente"
        },
        // HEADER
        PuntoVenta: null,
        NumComprobante: null,
        // CLIENTES
        searchCliente: null,
        searchClienteTable: false,
        clientes: [],
        // VENCIMIENTO
        vencimiento: false,
        // ARTICULOS
        articulosPanel: [],
        articulos: [],
        articuloSelected: {},
        // COTIZACION
        cotizacion: 1,
        fechaCotizacion: "",
        dialogCotizacion: false,
        // DETALLES
        detalles: [],
        // SUBTOTAL
        subtotal: null,
        // MODALS
        detallesDialog: false
    }),

    computed: {
        // ARTICULOS
        cantidadLitros: {
            set() {},
            get() {
                if (this.articuloSelected.cantidad) {
                    return Number(
                        this.articuloSelected.cantidad *
                            this.articuloSelected.litros
                    );
                } else {
                    return null;
                }
            }
        },

        // COTIZACION
        dolares: {
            set() {},
            get() {
                if (this.articuloSelected) {
                    if (
                        this.articuloSelected.precio &&
                        this.articuloSelected.cantidad
                    ) {
                        return Number(
                            this.articuloSelected.precio *
                                this.articuloSelected.cantidad *
                                this.articuloSelected.litros
                        );
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            }
        },

        pesos: {
            set() {},
            get() {
                if (this.dolares && this.cotizacion) {
                    return Number(this.dolares * this.cotizacion).toFixed(2);
                }
            }
        },

        // TOTALES
        total: {
            set() {},
            get() {
                if (this.subtotal) {
                    let total = this.subtotal;
                    let bonificacion = 0;
                    let recargo = 0;

                    if (this.$store.state.presupuestos.form.bonificacion) {
                        bonificacion =
                            Number(
                                this.$store.state.presupuestos.form
                                    .bonificacion * this.subtotal
                            ) / 100;
                    }

                    if (this.$store.state.presupuestos.form.recargo) {
                        recargo =
                            Number(
                                this.$store.state.presupuestos.form.recargo *
                                    this.subtotal
                            ) / 100;
                    }

                    return total - bonificacion + recargo;
                } else {
                    return null;
                }
            }
        }
    },

    async mounted() {
        this.inProcess = true;
        await this.checkCurrency();
        await this.getPoint();
        await this.getArticles();
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
                if (this.detalles.length <= 0) {
                    return false;
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

        // HEADER
        getPoint: async function() {
            let data;
            await axios
                .get("/api/config")
                .then(response => {
                    data = response.data;
                })
                .catch(error => {
                    console.log(error);
                });

            this.PuntoVenta = data.puntoventa;
            let response = await this.$store.dispatch("presupuestos/index");
            if (response.ultimo) {
                this.NumComprobante =
                    Number(response.ultimo.numpresupuesto) + 1;
            } else {
                this.NumComprobante = data.numpresupuesto;
            }
        },

        // CLIENTES
        searchClienteAfter() {
            this.searchInProcess = true;
            this.searchClienteTable = true;
            if (this.searchCliente != null && this.searchCliente != "") {
                if (this.searchCliente == "0") {
                    this.searchCliente = "CONSUMIDOR FINAL";
                    this.$store.state.presupuestos.form.cliente_id = 1;
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
            this.$store.state.presupuestos.form.cliente_id = null;
            axios
                .post("/api/buscando", {
                    buscar: this.searchCliente,
                    nuevoComp: true
                })
                .then(response => {
                    this.clientes = response.data.clientes;
                    this.searchInProcess = false;
                })
                .catch(error => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        selectCliente(cliente) {
            this.searchCliente = cliente.razonsocial;
            this.$store.state.presupuestos.form.cliente_id = cliente.id;
            this.clientes = [];
            this.searchClienteTable = false;
        },

        // ARTICULOS
        getArticles: async function() {
            let response = await this.$store.dispatch("articulos/index");
            this.articulos = response.articulos;
        },

        selectArticulo(articulo) {
            this.articuloSelected = Object.assign({ cantidad: 1 }, articulo);
            this.articulosPanel = [];
            this.disabled.detalles = false;
        },

        // DETALLES
        addDetail: async function() {
            if (this.$refs.detailForm.validate()) {
                await this.pushDetail();

                this.disabled.detalles = true;
                this.articuloSelected = {};
                this.searchArticulo = null;
                this.$refs.detailForm.reset();
            }
        },

        pushDetail() {
            let detailData = {
                cantidadLitros: this.cantidadLitros,
                subtotalPesos: this.pesos,
                subtotalDolares: this.dolares,
                cotizacion: this.cotizacion,
                fechaCotizacion: this.fechaCotizacion
            };

            let detail = Object.assign(this.articuloSelected, detailData);

            if (this.detalles.length > 0) {
                let nuevoDetalle = true;
                for (let i = 0; i < this.detalles.length; i++) {
                    if (this.detalles[i].precio == detail.precio) {
                        this.detalles[i].cantidad = Number(
                            Number(this.detalles[i].cantidad) +
                                Number(detail.cantidad)
                        );

                        this.detalles[i].subtotalDolares =
                            Number(this.detalles[i].precio) *
                            Number(this.detalles[i].cantidad);

                        this.detalles[i].subtotalPesos =
                            Number(this.detalles[i].subtotalDolares) *
                            Number(this.cotizacion);

                        nuevoDetalle = false;
                    }
                }

                if (nuevoDetalle) {
                    this.detalles.push(detail);
                }
            } else {
                this.detalles.push(detail);
            }

            this.subtotalControl();
        },

        deleteDetail(detalle) {
            let index = this.detalles.indexOf(detalle);
            this.detalles.splice(index, 1);
        },

        // SUBTOTAL
        subtotalControl() {
            if (this.detalles.length > 0) {
                let sub = 0;
                for (let i = 0; i < this.detalles.length; i++) {
                    sub += this.detalles[i].subtotalDolares;
                }
                this.subtotal = sub;
            }
        },

        // FORM
        setData: async function() {
            if (
                this.$refs.presupuestosClienteForm.validate() &&
                this.$refs.presupuestosTotalesForm.validate()
            ) {
                if (this.detalles.length <= 0) {
                    this.detallesDialog = true;
                    return false;
                } else {
                    this.$store.state.presupuestos.form.subtotal = this.subtotal;
                    this.$store.state.presupuestos.form.total = this.total;
                    this.$store.state.presupuestos.form.totalPesos = this.totalPesos;
                    this.$store.state.presupuestos.form.detalles = this.detalles;
                    this.$store.state.presupuestos.form.cotizacion = this.cotizacion;
                    this.$store.state.presupuestos.form.fechaCotizacion = this.fechaCotizacion;
                    this.$store.state.presupuestos.form.numpresupuesto = this.NumComprobante;
                    return true;
                }
            }
        },

        resetData: async function() {
            this.clientes = [];
            this.detalles = [];
            this.articuloSelected = {};
            this.$refs.presupuestosClienteForm.reset();
            this.$refs.presupuestosTotalesForm.reset();
            this.step = 1;
            this.checkCurrency();
        }
    }
};
</script>

<style lang="scss"></style>
