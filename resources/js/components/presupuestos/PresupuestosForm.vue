<template>
    <div>
        <v-card shaped outlined>
            <!-- HEADER -->
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="6" align-self="center">Nuevo Presupuesto</v-col>
                    <v-col cols="6">
                        <v-list-item two-line class="text-right">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <b>Comprobante N°:&nbsp;</b>
                                    {{ NumComprobante }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    <b>Punto de venta:&nbsp;</b>
                                    {{ PuntoVenta }}
                                </v-list-item-subtitle>
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
                            <v-form ref="CreatePresupuesto" @submit.prevent="savePresupuesto()">
                                <v-row justify="space-around">
                                    <!-- CLIENTE -->
                                    <v-col cols="12" sm="7" class="py-0">
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
                                                        Ningun dato coincide con
                                                        lel criterio de busqueda
                                                    </h3>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
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
                                                        $store.state
                                                            .presupuestos.form
                                                            .vencimiento
                                                    "
                                                    label="Vencimiento"
                                                    readonly
                                                    outlined
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="
                                                    $store.state.presupuestos
                                                        .form.vencimiento
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
                                                                .presupuestos
                                                                .form
                                                                .vencimiento
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
                                                    >{{articuloSelected.articulo}}</div>
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
                                    <v-expand-transition>
                                        <v-form
                                            ref="detailForm"
                                            @submit.prevent="addDetail"
                                            v-if="!disabled.detalles"
                                        >
                                            <v-row justify="center" class="px-3">
                                                <!-- DETALLES -->
                                                <v-col cols="12" sm="4" class="py-0">
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
                                                <v-col cols="12" sm="4" class="py-0">
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

                                    <!-- TABLA DETALLES -->
                                    <v-col cols="12" class="py-0 mb-5">
                                        <v-card outlined>
                                            <v-simple-table>
                                                <template v-slot:default>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">Articulo</th>
                                                            <th
                                                                class="text-left hidden-sm-and-down"
                                                            >Precio</th>
                                                            <th class="text-left">Unidades</th>

                                                            <th class="text-left">Subtotal</th>
                                                            <th
                                                                class="text-left hidden-sm-and-down"
                                                            >
                                                                Subtotal en
                                                                pesos
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
                                    <v-col cols="12" sm="6" class="py-0 px-0">
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="
                                                    $store.state.presupuestos
                                                        .form.bonificacion
                                                "
                                                type="number"
                                                label="Bonificacion %"
                                                outlined
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="
                                                    $store.state.presupuestos
                                                        .form.recargo
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
                                                $store.state.presupuestos.form
                                                    .observaciones
                                            "
                                            outlined
                                            label="Observaciones"
                                            no-resize
                                        ></v-textarea>
                                    </v-col>
                                </v-row>
                                <v-row justify="center">
                                    <v-btn
                                        tile
                                        outlined
                                        color="secondary"
                                        class="mx-2 elevation-0"
                                        @click="resetForm()"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        type="submit"
                                        tile
                                        color="secondary"
                                        class="mx-2 elevation-0"
                                    >Guardar</v-btn>
                                </v-row>
                                <br />
                            </v-form>

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
            detalles: true
        },
        rules: {
            required: value => !!value || "Este campo es obligatorio"
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

        // Totales
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
    mounted: async function() {
        this.inProcess = true;
        await this.getPoint();
        await this.getArticles();
        await this.consultarDivisa();
        this.inProcess = false;
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
                this.consultarDivisa();
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

        savePresupuesto: async function() {
            if (this.$refs.CreatePresupuesto.validate()) {
                if (this.detalles.length <= 0) {
                    this.detallesDialog = true;
                } else {
                    this.inProcess = true;

                    this.$store.state.presupuestos.form.subtotal = this.subtotal;
                    this.$store.state.presupuestos.form.total = this.total;
                    this.$store.state.presupuestos.form.totalPesos = this.totalPesos;
                    this.$store.state.presupuestos.form.detalles = this.detalles;
                    this.$store.state.presupuestos.form.cotizacion = this.cotizacion;
                    this.$store.state.presupuestos.form.fechaCotizacion = this.fechaCotizacion;
                    this.$store.state.presupuestos.form.numpresupuesto = this.NumComprobante;
                    let response = await this.$store.dispatch(
                        "presupuestos/save"
                    );
                    this.resetForm();
                    await this.getPoint();
                    this.inProcess = false;
                    this.$router.push("/presupuestos");
                }
            }
        },

        resetForm: async function() {
            await this.$refs.CreatePresupuesto.reset();
            this.clientes = [];
            this.detalles = [];
        }
    }
};
</script>

<style lang="scss">
.search-client-table {
    border-top: none !important;
    border-top-left-radius: 0px !important;
    border-top-right-radius: 0px !important;
    margin-top: -30px;
}

.search-client-input {
    .v-input__control {
        .v-input__slot {
            border-bottom-left-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }
    }
}

.search-client-select {
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
