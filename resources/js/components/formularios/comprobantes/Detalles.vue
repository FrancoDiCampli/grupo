<template>
    <v-row justify="space-around" class="my-1">
        <!-- ARTICULOS -->
        <v-col cols="12" class="py-0 articulos-panel">
            <v-expansion-panels v-model="articulosPanel">
                <v-expansion-panel>
                    <v-expansion-panel-header expand-icon="fas fa-caret-down" @click="getData()">
                        <div v-if="articuloSelected.articulo">{{ articuloSelected.articulo }}</div>
                        <div v-else>Articulos</div>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div v-if="inProcess" class="py-5">
                            <h3 class="text-center">Cargando articulos...</h3>
                        </div>
                        <div v-else>
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
                                        v-for="(articulo, index) in articulos"
                                        :key="index"
                                        :class="articulo.stock > 0 ? 'search-articulo-select' : ''"
                                        @click="selectArticulo(articulo)"
                                    >
                                        <td>{{articulo.codarticulo}}</td>
                                        <td>{{articulo.articulo}}</td>
                                        <td>{{articulo.precio}}</td>
                                        <td>{{articulo.stock}}</td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                            <div v-else class="py-5">
                                <h3 class="text-center">
                                    No existe ningún
                                    articulo registrado
                                </h3>
                            </div>
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
                        :disabled="disabledDetalles"
                        label="Precio"
                        required
                        outlined
                        type="number"
                    ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4" class="py-0">
                    <v-text-field
                        v-model="articuloSelected.cantidad"
                        :rules="[rules.required, rules.cantidadMaxima]"
                        :disabled="disabledDetalles"
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
                    :disabled="disabledDetalles"
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
                    :width="$vuetify.breakpoint.xsOnly ? '100%': '300px'"
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="latinDate"
                            label="Fecha de la cotización"
                            :disabled="disabledDetalles"
                            readonly
                            outlined
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker v-model="fechaCotizacion" scrollable locale="es">
                        <v-spacer></v-spacer>
                        <v-btn
                            text
                            color="primary"
                            @click="$refs.dialogCotizacion.save(fechaCotizacion)"
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
                        :disabled="disabledDetalles"
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
                                <th class="text-left hidden-sm-and-down">Subtotal en pesos</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(detalle, index) in detalles" :key="index">
                                <td>{{ detalle.articulo }}</td>
                                <td class="hidden-sm-and-down">{{ detalle.precio }}</td>
                                <td>{{ detalle.cantidad }}</td>
                                <td>{{detalle.subtotalDolares}}</td>
                                <td class="hidden-sm-and-down">{{detalle.subtotalPesos}}</td>
                                <td>
                                    <v-btn icon color="secondary" @click="deleteDetail(detalle)">
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
</template>

<script>
var cantidadMaxima = 999999999;

import moment from "moment";

export default {
    data: () => ({
        inProcess: false,
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            cantidadMaxima: value =>
                value <= Number(cantidadMaxima) ||
                "La cantidad no puede superar el stock existente"
        },
        // ARTICULOS
        articulosPanel: [],
        articulos: [],
        articuloSelected: {},
        // COTIZACION
        cotizacion: null,
        fechaCotizacion: moment(),
        dialogCotizacion: false,
        // DETALLES
        detalles: [],
        disabledDetalles: true,
        // SUBTOTAL
        subtotal: null
    }),

    props: ["maximum"],

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
        latinDate() {
            let date = moment(this.fechaCotizacion).format("DD-MM-YYYY");
            return date;
        },

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
        }
    },

    methods: {
        getData: async function() {
            this.inProcess = true;
            await this.checkCurrency();
            await this.getArticles();
            this.inProcess = false;
        },

        // COTIZACIÓN
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

        // ARTICULOS
        getArticles: async function() {
            let response = await this.$store.dispatch("articulos/index");
            this.articulos = response.articulos;
        },

        selectArticulo(articulo) {
            if (articulo.stock > 0) {
                this.articuloSelected = Object.assign(
                    { cantidad: 1 },
                    articulo
                );

                // Establecer la cantidad maxima a vender
                if (this.maximum) {
                    cantidadMaxima = articulo.stock;
                    if (this.detalles.length > 0) {
                        let stockExistente = 0;
                        for (let i = 0; i < this.detalles.length; i++) {
                            if (this.detalles[i].id == articulo.id) {
                                stockExistente += Number(
                                    this.detalles[i].cantidad
                                );
                            }
                        }
                        cantidadMaxima = cantidadMaxima - stockExistente;
                    }
                }

                this.articulosPanel = [];
                this.disabledDetalles = false;
            }
        },

        // DETALLES
        addDetail: async function() {
            if (this.$refs.detailForm.validate()) {
                await this.pushDetail();

                this.disabledDetalles = true;
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

        getSubtotal() {
            return this.subtotal;
        },

        getDetalles() {
            return this.detalles;
        },

        resetForm() {
            this.detalles = [];
        }
    }
};
</script>

<style>
</style>