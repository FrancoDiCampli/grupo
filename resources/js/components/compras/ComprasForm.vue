<template>
    <div>
        <v-card shaped outlined :loading="inProcess" class="pb-4">
            <!-- HEADER -->
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="auto" align-self="center">Nueva compra</v-col>
                    <v-spacer></v-spacer>
                    <v-col cols="auto">
                        <v-list-item two-line class="text-right">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <b v-if="$vuetify.breakpoint.xsOnly"
                                        >P:&nbsp;</b
                                    >
                                    <b v-else>Punto de venta:&nbsp;</b>
                                    {{ PuntoVenta }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-col>
                </v-row>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-0 ma-0">
                <v-stepper v-model="step" vertical class="elevation-0">
                    <!-- PROVEEDOR, FECHA, COMPROBANTE -->
                    <v-stepper-step
                        :complete="step > 1"
                        step="1"
                        :editable="true"
                        edit-icon="fas fa-pen"
                        :rules="[
                            () => validateStep(1, 'comprasProveedoresForm')
                        ]"
                        >Proveedor</v-stepper-step
                    >
                    <v-stepper-content step="1">
                        <v-form ref="comprasProveedoresForm">
                            <v-row justify="space-around" class="my-1">
                                <!-- PROVEEDOR -->
                                <v-col cols="12" class="py-0">
                                    <v-text-field
                                        v-model="searchProveedor"
                                        :rules="[rules.required]"
                                        @keyup="searchProveedorAfter()"
                                        :disabled="disabled.proveedor"
                                        class="search-proveedor-input"
                                        append-icon="fas fa-search"
                                        label="Proveedor"
                                        outlined
                                    ></v-text-field>
                                    <v-card
                                        outlined
                                        class="search-proveedor-table mb-5"
                                        v-if="searchProveedorTable"
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
                                                searchProveedor != null &&
                                                    searchProveedor != ''
                                            "
                                        >
                                            <v-simple-table
                                                v-if="proveedores.length > 0"
                                            >
                                                <thead>
                                                    <tr>
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            Apellido y nombre
                                                        </th>
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            CUIT
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="(proveedor,
                                                        index) in proveedores"
                                                        :key="index"
                                                        class="search-client-select"
                                                        @click="
                                                            selectProveedor(
                                                                proveedor
                                                            )
                                                        "
                                                    >
                                                        <td>
                                                            {{
                                                                proveedor.razonsocial
                                                            }}
                                                        </td>
                                                        <td>
                                                            {{ proveedor.cuit }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </v-simple-table>
                                            <div v-else class="py-5">
                                                <h3 class="text-center">
                                                    Ningun dato coincide con el
                                                    criterio de busqueda
                                                </h3>
                                            </div>
                                        </div>
                                    </v-card>
                                </v-col>
                                <!-- N° REMITO -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-text-field
                                        v-model="
                                            $store.state.compras.form.numcompra
                                        "
                                        :rules="[rules.required]"
                                        label="N° remito"
                                        type="number"
                                        required
                                        outlined
                                    ></v-text-field>
                                </v-col>
                                <!-- FECHA -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-dialog
                                        ref="dialogFecha"
                                        v-model="fechaDialog"
                                        :return-value.sync="
                                            $store.state.compras.form.fecha
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
                                                    $store.state.compras.form
                                                        .fecha | formatDate
                                                "
                                                @input="
                                                    value =>
                                                        (store.state.compras.form.fecha = value)
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
                                                $store.state.compras.form.fecha
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
                                                        $store.state.compras
                                                            .form.fecha
                                                    )
                                                "
                                                >Aceptar</v-btn
                                            >
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
                                >Continuar</v-btn
                            >
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
                        <small v-if="!validateDetail(2)"
                            >Debe ingresar al menos un detalle.</small
                        >
                    </v-stepper-step>
                    <v-stepper-content step="2">
                        <v-row justify="space-around" class="my-1">
                            <!-- ARTICULOS -->
                            <v-col cols="12" class="py-0 articulos-panel">
                                <v-expansion-panels v-model="articulosPanel">
                                    <v-expansion-panel>
                                        <v-expansion-panel-header
                                            expand-icon="fas fa-caret-down"
                                        >
                                            <div
                                                v-if="articuloSelected.articulo"
                                            >
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
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            Codigo
                                                        </th>
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            Articulo
                                                        </th>
                                                        <th
                                                            class="text-xs-left"
                                                        >
                                                            Precio
                                                        </th>
                                                        <th
                                                            class="text-xs-left"
                                                        >
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
                            <v-col cols="12" class="py-0">
                                <v-expand-transition>
                                    <v-row
                                        justify="center"
                                        v-if="searchInProcess"
                                    >
                                        <v-progress-circular
                                            :size="70"
                                            :width="7"
                                            color="primary"
                                            indeterminate
                                        ></v-progress-circular>
                                    </v-row>
                                    <v-form
                                        ref="detailForm"
                                        @submit.prevent="addDetail"
                                        v-else-if="!disabled.detalles"
                                    >
                                        <v-row justify="center">
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                class="py-0"
                                            >
                                                <v-select
                                                    v-model="
                                                        articuloSelected.movimiento
                                                    "
                                                    disabled
                                                    :items="movimientos"
                                                    :rules="[rules.required]"
                                                    label="Movimiento"
                                                    outlined
                                                ></v-select>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                class="py-0"
                                            >
                                                <v-text-field
                                                    v-model="
                                                        articuloSelected.cantidad
                                                    "
                                                    :rules="[rules.required]"
                                                    label="Unidades"
                                                    required
                                                    outlined
                                                    type="number"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                class="py-0"
                                            >
                                                <v-text-field
                                                    v-model="
                                                        articuloSelected.litros
                                                    "
                                                    :rules="[rules.required]"
                                                    label="Litros"
                                                    required
                                                    outlined
                                                    disabled
                                                    type="number"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                class="py-0"
                                            >
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
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                class="py-0"
                                            >
                                                <v-text-field
                                                    v-model="
                                                        articuloSelected.precio
                                                    "
                                                    :rules="[rules.required]"
                                                    label="Precio"
                                                    required
                                                    outlined
                                                    type="number"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                class="py-0"
                                            >
                                                <v-text-field
                                                    v-model="subtotalDetalle"
                                                    :rules="[rules.required]"
                                                    label="Subtotal"
                                                    required
                                                    outlined
                                                    disabled
                                                    type="number"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row justify="center" class="mb-5">
                                            <v-btn
                                                type="submit"
                                                outlined
                                                tile
                                                color="secondary"
                                                >Añadir detalle</v-btn
                                            >
                                        </v-row>
                                    </v-form>
                                </v-expand-transition>
                            </v-col>
                            <!-- TABLA DETALLES -->
                            <v-col cols="12" class="py-0 mb-5">
                                <v-alert type="error" v-if="errorAlert">
                                    Uno o más pedidos de los detalles no
                                    corresponden al proveedor seleccionado. Por
                                    favor, elimina los detalles en conflicto o
                                    selecciona otro proveedor
                                </v-alert>
                                <v-card outlined>
                                    <v-simple-table>
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th class="text-left">
                                                        Actualizar
                                                    </th>
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
                                                        <div
                                                            v-if="
                                                                !detalle.error
                                                            "
                                                        >
                                                            <v-checkbox
                                                                v-model="
                                                                    detalle.update
                                                                "
                                                            ></v-checkbox>
                                                        </div>
                                                        <div v-else>
                                                            <v-icon
                                                                size="medium"
                                                                color="red"
                                                            >
                                                                fas
                                                                fa-exclamation-triangle
                                                            </v-icon>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ detalle.articulo }}
                                                    </td>
                                                    <td
                                                        class="hidden-sm-and-down"
                                                    >
                                                        {{ detalle.precio }}
                                                    </td>
                                                    <td>
                                                        {{ detalle.cantidad }}
                                                    </td>
                                                    <td>
                                                        {{ detalle.subtotal }}
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
                                                            <v-icon
                                                                size="medium"
                                                                >fasfa-times</v-icon
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
                        :rules="[() => validateStep(3, 'comprasTotalesForm')]"
                        >Bonificación y recargo.</v-stepper-step
                    >
                    <v-stepper-content step="3">
                        <v-form ref="comprasTotalesForm">
                            <v-row justify="space-around" class="my-1">
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.compras.form
                                                    .bonificacion
                                            "
                                            type="number"
                                            label="Bonificacion %"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.compras.form
                                                    .recargo
                                            "
                                            type="number"
                                            label="Recargo %"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                </v-col>
                                <v-col cols="12" sm="6" class="py-0 px-0">
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="subtotal"
                                            :rules="[rules.required]"
                                            type="number"
                                            label="Subtotal"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="total"
                                            :rules="[rules.required]"
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
                                        $store.state.compras.form.observaciones
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
    </div>
</template>

<script>
export default {
    data: () => ({
        step: 1,
        // GENERAL
        inProcess: false,
        searchInProcess: false,
        disabled: {
            proveedor: false,
            articulo: true,
            detalles: false,
            movimientos: false
        },
        rules: {
            required: value => !!value || "Este campo es obligatorio"
        },
        // HEADER
        PuntoVenta: null,
        // PROVEEDORES
        searchProveedor: null,
        searchProveedorTable: false,
        proveedores: [],
        // ARTICULOS
        articulosPanel: [],
        articulos: [],
        articuloSelected: {},
        // DETALLES
        movimientos: ["ALTA", "INCREMENTO"],
        detalles: [],
        // SUBTOTAL
        subtotal: null,
        // DIALOGS
        fechaDialog: false,
        errorAlert: false,
        detallesDialog: false
    }),

    computed: {
        // DETALLES
        cantidadLitros: {
            set() {},
            get() {
                if (this.articuloSelected.cantidad) {
                    let cantidad = Number(this.articuloSelected.cantidad);
                    let litros = Number(this.articuloSelected.litros);
                    return cantidad * litros;
                } else {
                    return null;
                }
            }
        },

        subtotalDetalle: {
            set() {},
            get() {
                if (
                    this.articuloSelected.precio &&
                    this.articuloSelected.cantidad
                ) {
                    let cantidad = Number(this.articuloSelected.cantidad);
                    let precio = Number(this.articuloSelected.precio);

                    return cantidad * precio;
                } else {
                    return null;
                }
            }
        },

        // TOTAL
        // TODO:
        total: {
            set() {},
            get() {
                if (this.subtotal) {
                    let total = this.subtotal;
                    let bonificacion = 0;
                    let recargo = 0;

                    if (this.$store.state.compras.form.bonificacion) {
                        bonificacion =
                            Number(
                                this.$store.state.compras.form.bonificacion *
                                    this.subtotal
                            ) / 100;
                    }

                    if (this.$store.state.compras.form.recargo) {
                        recargo =
                            Number(
                                this.$store.state.compras.form.recargo *
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

    mounted: async function() {
        this.inProcess = true;
        await this.getPoint();
        await this.getArticles();
        this.inProcess = false;
    },

    methods: {
        // HEADER
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
        },

        // PROVEEDORES
        searchProveedorAfter() {
            this.searchInProcess = true;
            this.searchProveedorTable = true;
            this.disabled.articulo = true;
            if (this.searchProveedor != null && this.searchProveedor != "") {
                if (this.searchProveedor == "0") {
                    this.searchProveedor = "GRUPO APC";
                    this.$store.state.compras.form.supplier_id = 1;
                    this.proveedores = [];
                    this.searchProveedorTable = false;
                    this.searchInProcess = false;
                    this.disabled.articulo = false;
                } else {
                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }
                    this.timer = setTimeout(() => {
                        this.findProveedor();
                    }, 1000);
                }
            }
        },

        findProveedor: async function() {
            this.$store.state.compras.form.supplier_id = null;
            axios
                .post("/api/buscando", { buscar: this.searchProveedor })
                .then(response => {
                    this.proveedores = response.data.proveedores;
                    this.searchInProcess = false;
                })
                .catch(error => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        selectProveedor(proveedor) {
            this.searchProveedor = proveedor.razonsocial;
            this.$store.state.compras.form.supplier_id = proveedor.id;
            this.proveedores = [];
            this.searchProveedorTable = false;
            this.disabled.articulo = false;
            this.checkProveedor();
        },

        checkProveedor() {
            if (this.detalles.length > 0) {
                this.errorAlert = false;
                for (let i = 0; i < this.detalles.length; i++) {
                    if (
                        this.detalles[i].supplier_id !=
                        this.$store.state.compras.form.supplier_id
                    ) {
                        if (this.detalles[i].movimiento != "ALTA") {
                            this.detalles[i].error = true;
                            this.errorAlert = true;
                        } else {
                            this.detalles[
                                i
                            ].supplier_id = this.$store.state.compras.form.supplier_id;
                        }
                    }
                }
            }
        },

        // ARTICULOS
        getArticles: async function() {
            let response = await this.$store.dispatch("articulos/index");
            this.articulos = response.articulos;
        },

        selectArticulo: async function(articulo) {
            let movimiento;
            if (articulo.stock > 0) {
                movimiento = "INCREMENTO";
            } else {
                movimiento = "ALTA";
            }

            this.articuloSelected = {
                cantidad: 1,
                totalLitros: articulo.litros,
                movimiento: movimiento,
                supplier_id: this.$store.state.compras.form.supplier_id,
                articulo_id: articulo.id,
                codarticulo: articulo.codarticulo,
                articulo: articulo.articulo,
                medida: articulo.medida,
                litros: articulo.litros,
                precio: articulo.precio,
                subtotal: articulo.precio,
                error: false,
                update: false
            };
            this.articulosPanel = [];
            this.disabled.detalles = false;
        },

        addDetail: async function() {
            if (this.$refs.detailForm.validate()) {
                await this.pushDetail();

                this.disabled.detalles = true;
                this.articuloSelected = {};
                this.searchArticulo = null;
            }
        },

        pushDetail() {
            this.articuloSelected.totalLitros = this.cantidadLitros;
            this.articuloSelected.subtotal = this.subtotalDetalle;

            let detail = this.articuloSelected;

            if (this.detalles.length > 0) {
                let nuevoDetalle = true;
                for (let i = 0; i < this.detalles.length; i++) {
                    if (
                        this.detalles[i].precio == detail.precio &&
                        this.detalles[i].movimiento == detail.movimiento
                    ) {
                        // SE DEFINE LA NUEVA CANTIDAD
                        this.detalles[i].cantidad = Number(
                            Number(this.detalles[i].cantidad) +
                                Number(detail.cantidad)
                        );
                        // SE DEFINE LA CANTIDAD EN LITROS
                        this.detalles[i].cantidadLitros = Number(
                            Number(this.detalles[i].cantidad) *
                                Number(this.detalles[i].litros)
                        );
                        // SE DEFINE EL SUBTOTAL EN DOLARES
                        this.detalles[i].subtotalDolares =
                            Number(this.detalles[i].precio) *
                            Number(this.detalles[i].cantidadLitros);
                        // SE DEFINE EL SUBTOTAL EN PESOS
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
            this.checkProveedor();
        },

        // SUBTOTAL
        subtotalControl() {
            if (this.detalles.length > 0) {
                let sub = 0;
                for (let i = 0; i < this.detalles.length; i++) {
                    sub += this.detalles[i].subtotal;
                }
                this.subtotal = sub;
            }
        },

        // FORM
        setData: async function() {
            if (
                this.$refs.comprasProveedoresForm.validate() &&
                this.$refs.comprasTotalesForm.validate()
            ) {
                if (this.detalles.length <= 0) {
                    this.detallesDialog = true;
                    return false;
                } else {
                    this.$store.state.compras.form.subtotal = this.subtotal;
                    this.$store.state.compras.form.total = this.total;
                    this.$store.state.compras.form.detalles = this.detalles;
                    this.$store.state.compras.form.puntoventa = this.PuntoVenta;
                    return true;
                }
            }
        },

        resetData: async function() {
            this.proveedores = [];
            this.detalles = [];
            this.articuloSelected = {};
            this.$refs.comprasProveedoresForm.reset();
            this.$refs.comprasTotalesForm.reset();
            this.step = 1;
        }
    }
};
</script>

<style></style>
