<template>
    <div>
        <v-card shaped outlined>
            <!-- HEADER -->
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="6" align-self="center">Nueva Factura</v-col>
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
                            <v-form ref="CreateFactura" @submit.prevent="saveFactura()">
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
                                                v-else-if="searchCliente != null && searchCliente != ''"
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
                                                            v-for="(cliente, index) in clientes"
                                                            :key="index"
                                                            class="search-client-select"
                                                            @click="selectCliente(cliente)"
                                                        >
                                                            <td>{{ cliente.razonsocial }}</td>
                                                            <td>{{ cliente.documentounico }}</td>
                                                        </tr>
                                                    </tbody>
                                                </v-simple-table>
                                                <div v-else class="py-5">
                                                    <h3
                                                        class="text-center"
                                                    >Ningun dato coincide con lel criterio de busqueda</h3>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                    <!-- CONDICION VENTA -->
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="$store.state.facturas.form.condicionventa"
                                            label="Condición"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                    <!-- COMPROBANTE ADHERIDO -->
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="$store.state.facturas.form.comprobanteadherido"
                                            :disabled="disabled.cliente"
                                            label="Comprobante Adherido Nº"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
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
                                                            >Subtotal en pesos</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(detalle, index) in detalles"
                                                            :key="index"
                                                        >
                                                            <td>{{ detalle.articulo }}</td>
                                                            <td
                                                                class="hidden-sm-and-down"
                                                            >{{ detalle.precio }}</td>
                                                            <td>{{ detalle.cantidad }}</td>
                                                            <td>{{ detalle.subtotalDolares }}</td>
                                                            <td
                                                                class="hidden-sm-and-down"
                                                            >{{ detalle.subtotalPesos }}</td>
                                                        </tr>
                                                    </tbody>
                                                </template>
                                            </v-simple-table>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="12" sm="6" class="py-0 px-0">
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="$store.state.facturas.form.bonificacion"
                                                type="number"
                                                label="Bonificacion %"
                                                outlined
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="$store.state.facturas.form.recargo"
                                                type="number"
                                                label="Recargo %"
                                                outlined
                                            ></v-text-field>
                                        </v-col>
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
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="subtotal"
                                                type="number"
                                                label="Subtotal"
                                                outlined
                                                disabled
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="total"
                                                type="number"
                                                label="Total"
                                                outlined
                                                disabled
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" class="py-0">
                                            <v-text-field
                                                v-model="totalPesos"
                                                label="Total en pesos"
                                                outlined
                                                disabled
                                            ></v-text-field>
                                        </v-col>
                                    </v-col>
                                    <v-col cols="12" class="py-0">
                                        <v-textarea
                                            v-model="$store.state.facturas.form.observaciones"
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
                                        class="mx-2"
                                        to="/ventas"
                                    >Cancelar</v-btn>
                                    <v-btn
                                        :disabled="$store.state.facturas.form.cliente_id == null"
                                        type="submit"
                                        tile
                                        color="secondary"
                                        class="mx-2 elevation-0"
                                    >Guardar</v-btn>
                                </v-row>
                                <br />
                            </v-form>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
var cantidadMaxima = 999999999;

export default {
    data: () => ({
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
        // CONDICION
        condiciones: ["CONTADO"],
        // COTIZACION
        cotizacion: 1,
        fechaCotizacion: "",
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
        this.$refs.CreateFactura.reset();
        await this.consultarDivisa();
        await this.getPoint();
        this.subtotalControl();
        this.$store.state.facturas.form.tipocomprobante = "REMITO X";
        this.$store.state.facturas.form.condicionventa = "CONTADO";
        this.inProcess = false;
    },

    methods: {
        // GENERAL
        consultarDivisa() {
            return new Promise(resolve => {
                axios
                    .get("/api/consultar")
                    .then(response => {
                        this.cotizacion = response.data.valor;
                        this.fechaCotizacion = response.data.fecha;
                        resolve(response.data);
                    })
                    .catch(error => {
                        this.inProcess = false;
                        throw new Error(error);
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

        saveFactura: async function() {
            if (this.$refs.CreateFactura.validate()) {
                this.inProcess = true;
                this.$store.state.facturas.form.subtotal = this.subtotal;
                this.$store.state.facturas.form.total = this.total;
                this.$store.state.facturas.form.totalPesos = this.totalPesos;
                this.$store.state.facturas.form.detalles = this.detalles;
                this.$store.state.facturas.form.cotizacion = this.cotizacion;
                this.$store.state.facturas.form.fechaCotizacion = this.fechaCotizacion;
                this.$store.state.facturas.form.numfactura = this.NumComprobante;

                await this.$store.dispatch("facturas/save").then(() => {
                    this.resetForm();
                    this.$router.push("/ventas");
                });

                this.inProcess = false;
            }
        },

        resetForm: async function() {
            await this.$refs.CreateFactura.reset();
            this.clientes = [];
            this.detalles = [];
        }
    }
};
</script>

<style lang="scss">
</style>