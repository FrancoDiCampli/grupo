<template>
    <div>
        <v-card shaped outlined>
            <!-- HEADER -->
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="6" align-self="center">Nueva Compra</v-col>
                    <v-col cols="6">
                        <v-list-item two-line class="text-right">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <b>Punto de venta:&nbsp;</b>
                                    {{ PuntoVenta }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-col>
                </v-row>
            </v-card-title>
            <v-divider></v-divider>

            <v-card-text>
                <v-row justify="center">
                    <v-col cols="11">
                        <!-- INDICADOR DE CARGA -->
                        <div v-show="inProcess">
                            <v-row
                                justify="center"
                                align-content="center"
                                class="ma-0"
                                style="height: 90vh"
                            >
                                <v-progress-circular
                                    :size="70"
                                    :width="7"
                                    color="primary"
                                    indeterminate
                                ></v-progress-circular>
                            </v-row>
                        </div>
                        <div v-show="!inProcess">
                            <v-form ref="CreateCompra" @submit.prevent="saveCompra()">
                                <v-row justify="space-around">
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
                                                    v-if="
                                                        proveedores.length > 0
                                                    "
                                                >
                                                    <thead>
                                                        <tr>
                                                            <th class="text-xs-left">Apellido Nombre</th>
                                                            <th class="text-xs-left">CUIT</th>
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
                                                                {{
                                                                proveedor.cuit
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </v-simple-table>
                                                <div v-else class="py-5">
                                                    <h3 class="text-center">
                                                        Ningun dato coincide con
                                                        lel criterio de busqueda
                                                    </h3>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                    <!-- N° REMITO -->
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.compras.form
                                                    .numcompra
                                            "
                                            :rules="[rules.required]"
                                            label="N° Remito"
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
                                                    v-model="
                                                        $store.state.compras
                                                            .form.fecha
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
                                                    $store.state.compras.form
                                                        .fecha
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
                                                >Aceptar</v-btn>
                                            </v-date-picker>
                                        </v-dialog>
                                    </v-col>
                                    <!-- ARTICULOS -->
                                    <v-col cols="12" class="py-0 articulos-panel">
                                        <v-expansion-panels v-model="articulosPanel">
                                            <v-expansion-panel>
                                                <v-expansion-panel-header
                                                    expand-icon="fas fa-caret-down"
                                                >
                                                    <div
                                                        v-if="articuloSelected.articulo"
                                                    >{{ articuloSelected.articulo }}</div>
                                                    <div v-else>Articulos</div>
                                                </v-expansion-panel-header>
                                                <v-expansion-panel-content>
                                                    <v-simple-table
                                                        v-if="
                                                            articulos.length > 0
                                                        "
                                                    >
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
                                                                    articulo.stock >
                                                                    0
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
                                                                    {{
                                                                    articulo.stock
                                                                    }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </v-simple-table>
                                                    <div v-else class="py-5">
                                                        <h3 class="text-center">
                                                            No existe ningún
                                                            articulo registrado
                                                        </h3>
                                                    </div>
                                                </v-expansion-panel-content>
                                            </v-expansion-panel>
                                        </v-expansion-panels>
                                    </v-col>
                                    <v-col cols="12" class="py-0">
                                        <v-expand-transition>
                                            <v-form
                                                ref="detailForm"
                                                @submit.prevent="addDetail"
                                                v-if="!disabled.detalles"
                                            >
                                                <v-row justify="center">
                                                    <v-col cols="12" sm="6" class="py-0">
                                                        <v-select
                                                            v-model="
                                                            articuloSelected.movimiento
                                                        "
                                                            @change="
                                                            movimientoOnChange()
                                                        "
                                                            :disabled="
                                                            disabled.movimiento 
                                                        "
                                                            :items="movimientos"
                                                            :rules="[
                                                            rules.required
                                                        ]"
                                                            label="Movimiento"
                                                            outlined
                                                        ></v-select>
                                                    </v-col>
                                                    <v-col cols="12" sm="6" class="py-0">
                                                        <div v-if="disabled.lote">
                                                            <v-text-field
                                                                v-model="
                                                                articuloSelected.lote
                                                            "
                                                                :rules="[
                                                                rules.required
                                                            ]"
                                                                label="Pedido N°"
                                                                required
                                                                outlined
                                                                disabled
                                                                type="number"
                                                            ></v-text-field>
                                                        </div>
                                                        <div v-else>
                                                            <v-select
                                                                v-model="
                                                                articuloSelected.lote
                                                            "
                                                                :rules="[
                                                                rules.required
                                                            ]"
                                                                :items="lotes"
                                                                item-text="lote"
                                                                item-value="lote"
                                                                label="Pedido N°"
                                                                outlined
                                                            ></v-select>
                                                        </div>
                                                    </v-col>
                                                    <v-col cols="12" sm="4" class="py-0">
                                                        <v-text-field
                                                            v-model="
                                                            articuloSelected.cantidad
                                                        "
                                                            :rules="[
                                                            rules.required
                                                        ]"
                                                            label="Unidades"
                                                            required
                                                            outlined
                                                            type="number"
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="4" class="py-0">
                                                        <v-text-field
                                                            v-model="
                                                            articuloSelected.litros
                                                        "
                                                            :rules="[
                                                            rules.required
                                                        ]"
                                                            label="Litros"
                                                            required
                                                            outlined
                                                            disabled
                                                            type="number"
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="4" class="py-0">
                                                        <v-text-field
                                                            v-model="cantidadLitros"
                                                            :rules="[
                                                            rules.required
                                                        ]"
                                                            label="Cantidad en litros"
                                                            required
                                                            outlined
                                                            disabled
                                                            type="number"
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="6" class="py-0">
                                                        <v-text-field
                                                            v-model="
                                                            articuloSelected.precio
                                                        "
                                                            :rules="[
                                                            rules.required
                                                        ]"
                                                            label="Precio"
                                                            required
                                                            outlined
                                                            type="number"
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="6" class="py-0">
                                                        <v-text-field
                                                            v-model="
                                                            subtotalDetalle
                                                        "
                                                            :rules="[
                                                            rules.required
                                                        ]"
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
                                                    >Añadir detalle</v-btn>
                                                </v-row>
                                            </v-form>
                                        </v-expand-transition>
                                    </v-col>
                                    <!-- TABLA DETALLES -->
                                    <v-col cols="12" class="py-0 mb-5">
                                        <v-alert type="error" v-if="errorAlert">
                                            Uno o más pedidos de los detalles
                                            no corresponden al proveedor
                                            seleccionado. Por favor, elimina los
                                            detalles en conflicto o selecciona
                                            otro proveedor
                                        </v-alert>
                                        <v-card outlined>
                                            <v-simple-table>
                                                <template v-slot:default>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">Actualizar</th>
                                                            <th class="text-left">Articulo</th>
                                                            <th
                                                                class="text-left hidden-sm-and-down"
                                                            >Precio</th>
                                                            <th class="text-left">Unidades</th>

                                                            <th class="text-left">Subtotal</th>
                                                            <th class="text-left">Lote</th>
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
                                                                {{
                                                                detalle.articulo
                                                                }}
                                                            </td>
                                                            <td class="hidden-sm-and-down">
                                                                {{
                                                                detalle.precio
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                detalle.cantidad
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                detalle.subtotal
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                detalle.lote
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
                                    <v-col cols="12" class="py-0">
                                        <v-textarea
                                            v-model="
                                                $store.state.compras.form
                                                    .observaciones
                                            "
                                            outlined
                                            label="Observaciones"
                                            no-resize
                                        ></v-textarea>
                                    </v-col>
                                    <v-row justify="center">
                                        <v-btn
                                            tile
                                            outlined
                                            color="secondary"
                                            class="mx-2"
                                            @click="resetForm()"
                                        >Cancelar</v-btn>
                                        <v-btn
                                            type="submit"
                                            tile
                                            color="secondary"
                                            class="mx-2 elevation-0"
                                            :disabled="errorAlert"
                                        >Guardar</v-btn>
                                    </v-row>
                                </v-row>
                            </v-form>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import moment from "moment";

export default {
    data: () => ({
        // GENERAL
        inProcess: false,
        searchInProcess: false,
        disabled: {
            articulo: true,
            detalles: true,
            movimientos: false,
            lote: true
        },
        errorAlert: false,
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            cantidadMaxima: value =>
                value <= Number(cantidadMaxima) ||
                "La cantidad no puede superar el stock existente"
        },
        fechaDialog: false,
        // HEADER
        PuntoVenta: null,
        NumComprobante: null,
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
        lotes: [],
        detalles: [],
        // SUBTOTAL
        subtotal: null
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
        }
    },

    mounted: async function() {
        this.inProcess = true;
        this.$refs.CreateCompra.reset();
        await this.getPoint();
        await this.getArticles();
        await this.consultarDivisa();
        this.inProcess = false;
        this.searchProveedor = "GRUPO APC";
        this.$store.state.compras.form.supplier_id = 1;
    },

    methods: {
        // GENERAL
        consultarDivisa() {
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

        // ARTICULOS
        getArticles: async function() {
            let response = await this.$store.dispatch("articulos/index");
            this.articulos = response.articulos;
        },

        selectArticulo: async function(articulo) {
            this.searchInProcess = true;

            let response = await this.$store.dispatch("articulos/show", {
                id: articulo.id
            });

            // Establecer el proximo lote disponible
            let proximoLote = response.lotes.proximo;

            // Establecer todos los lotes disponibles
            for (let i = 0; i < response.inventarios.length; i++) {
                if (
                    response.inventarios[i].supplier_id ==
                    this.$store.state.compras.form.supplier_id
                ) {
                    this.lotes.push(response.inventarios[i]);
                }
            }

            // Habilitar o desabilitar los movimientos
            if (this.lotes.length > 0) {
                this.disabled.movimientos = false;
            } else {
                this.disabled.movimientos = true;
            }

            this.disabled.lote = true;

            this.articuloSelected = {
                cantidad: 1,
                totalLitros: articulo.litros,
                movimiento: "ALTA",
                lote: proximoLote,
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

        // DETALLES
        movimientoOnChange() {
            if (this.articuloSelected.movimiento == "ALTA") {
                this.disabled.lote = true;
            } else {
                this.disabled.lote = false;
            }
        },

        addDetail: async function() {
            if (this.$refs.detailForm.validate()) {
                await this.pushDetail();

                this.disabled.detalles = true;
                this.articuloSelected = {};
                this.lotes = [];
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
                        this.detalles[i].cantidad = Number(
                            Number(this.detalles[i].cantidad) +
                                Number(detail.cantidad)
                        );

                        this.detalles[i].subtotal =
                            Number(this.detalles[i].precio) *
                            Number(this.detalles[i].cantidad);

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

        saveCompra: async function() {
            if (
                this.$refs.CreateCompra.validate() &&
                this.detalles.length > 0
            ) {
                this.inProcess = true;

                this.$store.state.compras.form.subtotal = this.subtotal;
                this.$store.state.compras.form.total = this.total;
                this.$store.state.compras.form.detalles = this.detalles;
                this.$store.state.compras.form.puntoventa = this.PuntoVenta;
                await this.$store.dispatch("compras/save");
                await this.getPoint();
                this.inProcess = false;
                this.$router.push("/compras");
            }
        },

        resetForm: async function() {
            await this.$refs.CreateVenta.reset();
            this.proveedores = [];
            this.articulos = [];
            this.articuloSelected = {};
            this.detalles = [];
        }
    }
};
</script>

<style lang="scss" >
.search-proveedor-table {
    border-top: none !important;
    border-top-left-radius: 0px !important;
    border-top-right-radius: 0px !important;
    margin-top: -30px;
}

.search-proveedor-input {
    .v-input__control {
        .v-input__slot {
            border-bottom-left-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }
    }
}

.search-proveedor-select {
    cursor: pointer;
}

.search-articulo-table {
    border-top: none !important;
    border-top-left-radius: 0px !important;
    border-top-right-radius: 0px !important;
    margin-top: -30px;
}

.search-articulo-input {
    .v-input__control {
        .v-input__slot {
            border-bottom-left-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }
    }
}

.search-articulo-select {
    cursor: pointer;
}
</style>
