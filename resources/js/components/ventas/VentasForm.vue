<template>
    <div v-click-outside="nullFocus">
        <!-- HEADER -->
        <div @click="focus = 'header'">
            <v-card
                shaped
                outlined
                class="ventas-header"
                :class="focus == 'header' ? 'elevation-3' : ''"
                :loading="inProcess"
            >
                <v-card-title class="py-0 px-2">
                    <v-row class="pa-0 ma-0">
                        <v-col cols="auto" align-self="center"
                            >Nueva Venta</v-col
                        >
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
            </v-card>
        </div>

        <!-- CLIENTE, CONDICION, COMPROBANTE -->
        <div @click="focus = 'cliente'" class="mt-5">
            <v-card outlined :class="focus == 'cliente' ? 'elevation-3' : ''">
                <v-card-text
                    class="pt-10"
                    :class="$vuetify.breakpoint.xsOnly ? '' : 'px-8'"
                >
                    <v-row justify="space-around">
                        <!-- CLIENTE -->
                        <v-col cols="12" class="py-0">
                            <v-text-field
                                v-model="searchCliente"
                                :rules="[rules.required]"
                                @keyup="searchClienteAfter()"
                                :disabled="disabled.cliente"
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
                                    <v-simple-table v-if="clientes.length > 0">
                                        <thead>
                                            <tr>
                                                <th class="text-xs-left">
                                                    Apellido Nombre
                                                </th>
                                                <th class="text-xs-left">
                                                    Documento
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(cliente,
                                                index) in clientes"
                                                :key="index"
                                                class="search-client-select"
                                                @click="selectCliente(cliente)"
                                            >
                                                <td>
                                                    {{ cliente.razonsocial }}
                                                </td>
                                                <td>
                                                    {{ cliente.documentounico }}
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
                                    $store.state.ventas.form.comprobanteadherido
                                "
                                :disabled="disabled.cliente"
                                label="Comprobante Adherido Nº"
                                outlined
                                type="number"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </div>

        <!-- ARTICULOS, DETALLES -->
        <div @click="focus = 'articulos'" class="mt-5">
            <v-card outlined :class="focus == 'articulos' ? 'elevation-3' : ''">
                <v-card-text
                    class="pt-10"
                    :class="$vuetify.breakpoint.xsOnly ? '' : 'px-8'"
                >
                    <v-row justify="space-around">
                        <!-- ARTICULOS -->
                        <v-col cols="12" class="py-0 articulos-panel">
                            <v-expansion-panels v-model="articulosPanel">
                                <v-expansion-panel>
                                    <v-expansion-panel-header
                                        expand-icon="fas fa-caret-down"
                                    >
                                        <div v-if="articuloSelected.articulo">
                                            {{ articuloSelected.articulo }}
                                        </div>
                                        <div v-else>Articulos</div>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-simple-table
                                            v-if="articulos.length > 0"
                                        >
                                            <thead>
                                                <tr>
                                                    <th class="text-xs-left">
                                                        Codigo
                                                    </th>
                                                    <th class="text-xs-left">
                                                        Articulo
                                                    </th>
                                                    <th class="text-xs-left">
                                                        Precio
                                                    </th>
                                                    <th class="text-xs-left">
                                                        Stock
                                                    </th>
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
                                                        selectArticulo(articulo)
                                                    "
                                                >
                                                    <td>
                                                        {{
                                                            articulo.codarticulo
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{ articulo.articulo }}
                                                    </td>
                                                    <td>
                                                        {{ articulo.precio }}
                                                    </td>
                                                    <td>
                                                        {{ articulo.stock }}
                                                    </td>
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
                                <v-text-field
                                    v-model="fechaCotizacion"
                                    :rules="[rules.required]"
                                    label="Fecha de la cotización"
                                    outlined
                                    disabled
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-row justify="center" class="mb-5">
                                    <v-btn
                                        @click="addDetail()"
                                        outlined
                                        tile
                                        color="secondary"
                                        :disabled="disabled.detalles"
                                        >Añadir detalle</v-btn
                                    >
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

                                                <th class="text-left">
                                                    Subtotal
                                                </th>
                                                <th
                                                    class="text-left hidden-sm-and-down"
                                                >
                                                    Subtotal en pesos
                                                </th>
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
                                                <td class="hidden-sm-and-down">
                                                    {{ detalle.precio }}
                                                </td>
                                                <td>
                                                    {{ detalle.cantidad }}
                                                </td>
                                                <td>
                                                    {{
                                                        detalle.subtotalDolares
                                                    }}
                                                </td>
                                                <td class="hidden-sm-and-down">
                                                    {{ detalle.subtotalPesos }}
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
                                                            fas fa-times
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
                </v-card-text>
            </v-card>
        </div>

        <!-- BONIFICACION, RECARGO, SUBTOTAL, TOTAL -->
        <div @click="focus = 'total'" class="mt-5">
            <v-card outlined :class="focus == 'total' ? 'elevation-3' : ''">
                <v-card-text
                    class="pt-10"
                    :class="$vuetify.breakpoint.xsOnly ? '' : 'px-8'"
                >
                    <v-row justify="space-around">
                        <v-col cols="12" sm="6" class="py-0 px-0">
                            <!-- BONIFICACION -->
                            <v-col cols="12" class="py-0">
                                <v-text-field
                                    v-model="
                                        $store.state.ventas.form.bonificacion
                                    "
                                    type="number"
                                    label="Bonificacion %"
                                    outlined
                                ></v-text-field>
                            </v-col>
                            <!-- RECARGO -->
                            <v-col cols="12" class="py-0">
                                <v-text-field
                                    v-model="$store.state.ventas.form.recargo"
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
                            <v-col
                                cols="12"
                                class="py-0"
                                v-if="
                                    $store.state.ventas.form.condicionventa ==
                                        'CONTADO'
                                "
                            >
                                <v-text-field
                                    v-model="totalPesos"
                                    label="Total en pesos"
                                    outlined
                                    disabled
                                ></v-text-field>
                            </v-col>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </div>

        <!-- OBSERVACIONES, SAVE SLOT -->
        <div @click="focus = 'footer'" class="mt-5">
            <v-card
                outlined
                shaped
                class="ventas-footer"
                :class="focus == 'footer' ? 'elevation-3' : ''"
            >
                <v-card-text
                    class="pt-10"
                    :class="$vuetify.breakpoint.xsOnly ? '' : 'px-8'"
                >
                    <v-row justify="center">
                        <v-col cols="12" class="py-0">
                            <v-textarea
                                v-model="$store.state.ventas.form.observaciones"
                                outlined
                                label="Observaciones"
                                no-resize
                            ></v-textarea>
                        </v-col>
                        <slot></slot>
                    </v-row>
                </v-card-text>
            </v-card>
        </div>

        <v-dialog v-model="detallesDialog" width="500" persistent>
            <v-card>
                <v-card-title primary-title
                    >¡Ha ocurrido un error!</v-card-title
                >
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
                            >Cerrar</v-btn
                        >
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-model="condicionDialog" width="500" persistent>
            <v-card>
                <v-card-title primary-title
                    >¡Ha ocurrido un error!</v-card-title
                >
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
                            >Cerrar</v-btn
                        >
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import ClickOutside from "vue-click-outside";
var cantidadMaxima = 999999999;

export default {
    data: () => ({
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
        // CONDICION
        condicion: null,
        condiciones: ["CONTADO", "CUENTA CORRIENTE"],
        // ARTICULOS
        articulosPanel: [],
        articulos: [],
        articuloSelected: {},
        // COTIZACION
        cotizacion: 1,
        fechaCotizacion: "",
        // DETALLES
        detalles: [],
        // COMPROBANTES
        tipo: null,
        tipos: ["REMITO X", "NOTA DE PEDIDO"],
        // SUBTOTAL
        subtotal: null,
        // MODALS
        detallesDialog: false,
        condicionDialog: false
    }),

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

    directives: {
        ClickOutside
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

        nullFocus() {
            this.focus = null;
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

        // CLIENTES
        searchClienteAfter() {
            this.searchInProcess = true;
            this.searchClienteTable = true;
            if (this.searchCliente != null && this.searchCliente != "") {
                if (this.searchCliente == "0") {
                    this.searchCliente = "CONSUMIDOR FINAL";
                    this.$store.state.ventas.form.cliente_id = 1;
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
            this.$store.state.ventas.form.cliente_id = null;
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
            this.$store.state.ventas.form.cliente_id = cliente.id;
            this.clientes = [];
            this.searchClienteTable = false;
        },

        // ARTICULOS
        getArticles: async function() {
            let response = await this.$store.dispatch("articulos/index");
            this.articulos = response.articulos;
        },

        selectArticulo(articulo) {
            if (true) {
                this.articuloSelected = Object.assign(
                    { cantidad: 1 },
                    articulo
                );

                // Establecer la cantidad maxima a vender
                cantidadMaxima = articulo.stock;
                if (this.detalles.length > 0) {
                    let stockExistente = 0;
                    for (let i = 0; i < this.detalles.length; i++) {
                        if (this.detalles[i].id == articulo.id) {
                            stockExistente += Number(this.detalles[i].cantidad);
                        }
                    }
                    cantidadMaxima = cantidadMaxima - stockExistente;
                }

                this.articulosPanel = [];
                this.disabled.detalles = false;
            }
        },

        // DETALLES
        addDetail: async function() {
            if (this.$refs.detailForm.validate()) {
                await this.pushDetail();

                this.disabled.detalles = true;
                this.articuloSelected = {};
                this.searchArticulo = null;
                this.$refs.detailForm.reset();
                // this.checkCurrency();
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
        },

        resetData: async function() {
            this.clientes = [];
            this.detalles = [];
        }
    }
};
</script>

<style>
.ventas-header {
    border-bottom-right-radius: 4px !important;
}

.ventas-footer {
    border-top-left-radius: 4px !important;
}
</style>
