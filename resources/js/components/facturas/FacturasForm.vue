
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
                        :rules="[() => validateStep(1, 'facturasClienteForm')]"
                    >Cliente y condición de compra.</v-stepper-step>
                    <v-stepper-content step="1">
                        <v-form ref="facturasClienteForm">
                            <v-row justify="space-around" class="my-1">
                                <!-- CLIENTE -->
                                <v-col cols="12" class="py-0">
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

                                <!-- CONDICION VENTA -->
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-text-field
                                        v-model="$store.state.facturas.form.condicionventa"
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
                                        v-model="$store.state.facturas.form.comprobanteadherido"
                                        :rules="[rules.required]"
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

                    <!-- DETALLES -->
                    <v-stepper-step
                        :complete="step > 2"
                        step="2"
                        :editable="true"
                        edit-icon="fas fa-pen"
                    >Detalles.</v-stepper-step>
                    <v-stepper-content step="2">
                        <v-row justify="space-around" class="my-1">
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
                        :rules="[() => validateStep(3, 'facturasTotalesForm')]"
                    >Bonificación y recargo.</v-stepper-step>
                    <v-stepper-content step="3">
                        <v-form ref="facturasTotalesForm">
                            <v-row justify="space-around" class="my-1">
                                <v-col cols="12" sm="6" class="py-0 px-0">
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
                                                $store.state.facturas.form.recargo
                                            "
                                            type="number"
                                            label="Recargo %"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                    <!-- TIPO DE COMPROBANTE -->
                                    <v-col cols="12" class="py-0">
                                        <v-text-field
                                            v-model="$store.state.facturas.form.tipocomprobante"
                                            label="Tipo de comprobante"
                                            outlined
                                            disabled
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
    </div>
</template>


<script>
var cantidadMaxima = 999999999;

export default {
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
        // DETALLES
        detalles: [],
        // SUBTOTAL
        subtotal: null
    }),

    computed: {
        // Totales
        total: {
            set() {},
            get() {
                if (this.subtotal) {
                    let total = this.subtotal;
                    let bonificacion = 0;
                    let recargo = 0;

                    if (this.$store.state.facturas.form.bonificacion) {
                        bonificacion =
                            Number(
                                this.$store.state.facturas.form.bonificacion *
                                    this.subtotal
                            ) / 100;
                    }

                    if (this.$store.state.facturas.form.recargo) {
                        recargo =
                            Number(
                                this.$store.state.facturas.form.recargo *
                                    this.subtotal
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
                } else {
                    return null;
                }
            }
        }
    },

    created() {
        if (this.$store.state.facturas.form.detalles) {
            this.detalles = this.$store.state.facturas.form.detalles;
        } else {
            this.$router.push("/ventas");
        }
    },

    mounted: async function() {
        this.inProcess = true;
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
            axios
                .post("/api/buscando", { buscar: this.searchCliente })
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
            this.$store.state.facturas.form.cliente_id = cliente.id;
            this.clientes = [];
            this.searchClienteTable = false;
        },

        // SUBTOTAL
        subtotalControl() {
            if (this.detalles.length > 0) {
                let sub = 0;
                for (let i = 0; i < this.detalles.length; i++) {
                    sub += this.detalles[i].subtotalDolares * 1;
                }
                this.subtotal = Number(sub).toFixed(2);
            }
        },

        // FORM
        setData: async function() {
            if (this.$refs.facturasClienteForm.validate()) {
                this.$store.state.facturas.form.subtotal = this.subtotal;
                this.$store.state.facturas.form.total = this.total;
                this.$store.state.facturas.form.totalPesos = this.totalPesos;
                this.$store.state.facturas.form.detalles = this.detalles;
                this.$store.state.facturas.form.cotizacion = this.cotizacion;
                this.$store.state.facturas.form.fechaCotizacion = this.fechaCotizacion;
                this.$store.state.facturas.form.numfactura = this.NumComprobante;
                return true;
            }
        }
    }
};
</script>

<style lang="scss"></style>
