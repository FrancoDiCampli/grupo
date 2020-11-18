<template>
    <div>
        <v-slide-y-reverse-transition mode="out-in">
            <div v-if="!proveedor" key="formulario">
                <v-row justify="space-around" class="px-5">
                    <v-spacer></v-spacer>
                    <v-col cols="2">
                        <v-row justify="end">
                            <v-tooltip left>
                                <template v-slot:activator="{ on }">
                                    <v-btn
                                        v-on="on"
                                        v-show="
                                            $store.state.auth.user.rol ==
                                                'superAdmin' ||
                                                $store.state.auth.user.rol ==
                                                    'administrador'
                                        "
                                        text
                                        icon
                                        color="secondary"
                                        @click="formPanel = !formPanel"
                                    >
                                        <v-icon v-if="formPanel" size="medium">fas fa-times</v-icon>
                                        <v-icon v-else size="medium">fas fa-plus</v-icon>
                                    </v-btn>
                                </template>
                                <span v-if="formPanel">Cerrar</span>
                                <span v-else>Añadir o modificar inventarios</span>
                            </v-tooltip>
                        </v-row>
                    </v-col>
                </v-row>

                <v-expand-transition>
                    <v-card class="elevation-0" v-if="formPanel">
                        <v-card-text>
                            <v-form ref="inventariosForm" @submit.prevent="preventSave()">
                                <v-row justify="space-around">
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-select
                                            v-model="
                                                $store.state.inventarios.form
                                                    .movimiento
                                            "
                                            @change="movimientoOnChange()"
                                            :disabled="disabled.movimiento"
                                            :items="movimientos"
                                            :rules="[rules.required]"
                                            label="Movimiento"
                                            outlined
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="
                                                $store.state.inventarios.form
                                                    .cantidad
                                            "
                                            :rules="[
                                                rules.required,
                                                rules.cantidadMaxima
                                            ]"
                                            label="Cantidad"
                                            type="number"
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="cantidadLitros"
                                            :rules="[rules.required]"
                                            label="Cantidad en litros"
                                            type="number"
                                            outlined
                                            disabled
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" class="py-0">
                                        <v-text-field
                                            v-model="searchSupplier"
                                            @keyup="searchSupplierAfter()"
                                            :disabled="disabled.proveedor"
                                            class="search-supplier-input"
                                            append-icon="fas fa-search"
                                            label="Proveedor"
                                            outlined
                                        ></v-text-field>
                                        <v-card
                                            outlined
                                            class="search-supplier-table mb-5"
                                            v-if="searchSupplierTable"
                                        >
                                            <v-row justify="center" v-if="inProcess" class="py-5">
                                                <v-progress-circular
                                                    :size="70"
                                                    :width="7"
                                                    color="primary"
                                                    indeterminate
                                                ></v-progress-circular>
                                            </v-row>
                                            <div
                                                v-else-if="
                                                    searchSupplier != null &&
                                                        searchSupplier != ''
                                                "
                                            >
                                                <v-simple-table v-if="suppliers.length > 0">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-xs-left">Apellido / Nombre</th>
                                                            <th class="text-xs-left">Cuit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(supplier,
                                                            index) in suppliers"
                                                            :key="index"
                                                            class="search-supplier-select"
                                                            @click="
                                                                selectSupplier(
                                                                    supplier
                                                                )
                                                            "
                                                        >
                                                            <td>
                                                                {{
                                                                supplier.razonsocial
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                supplier.cuit
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </v-simple-table>
                                                <div v-else class="py-5">
                                                    <h3 class="text-center">
                                                        Ningun dato coincide con
                                                        el creterio de busqueda
                                                    </h3>
                                                    <v-row justify="center" class="mt-3">
                                                        <v-btn
                                                            v-model="proveedor"
                                                            color="secondary"
                                                            tile
                                                            class="elevation-0"
                                                            @click="
                                                                proveedor = true
                                                            "
                                                        >
                                                            Añadir
                                                            proveedor
                                                        </v-btn>
                                                    </v-row>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="12" class="py-0">
                                        <v-textarea
                                            v-model="
                                                $store.state.inventarios.form
                                                    .observaciones
                                            "
                                            placeholder="Observaciones"
                                            row-height="3"
                                            outlined
                                            no-resize
                                        ></v-textarea>
                                    </v-col>
                                </v-row>
                                <v-row justify="center">
                                    <v-btn
                                        type="submit"
                                        color="secondary"
                                        tile
                                        class="elevation-0"
                                    >Guardar</v-btn>
                                </v-row>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-expand-transition>

                <ArticulosInventarios></ArticulosInventarios>
            </div>
            <div v-else key="proveedores">
                <v-card-title>Nuevo proveedor</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form ref="ProveedoresCreate" @submit.prevent="saveProveedores">
                        <ProveedoresForm mode="create" ref="ProveedoresForm"></ProveedoresForm>
                        <v-row justify="center">
                            <v-btn
                                @click="cancelProveedores()"
                                :loading="$store.state.inProcess"
                                :disabled="$store.state.inProcess"
                                outlined
                                tile
                                color="secondary"
                                class="mx-2"
                            >Cancelar</v-btn>
                            <v-btn
                                type="submit"
                                tile
                                class="mx-2 elevation-0"
                                :loading="$store.state.inProcess"
                                :disabled="$store.state.inProcess"
                                color="secondary"
                            >Guardar</v-btn>
                        </v-row>
                    </v-form>
                </v-card-text>
            </div>
        </v-slide-y-reverse-transition>

        <v-dialog v-model="preventSaveDialog" width="750" persistent>
            <v-card>
                <v-card-title>¿Estás seguro?</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <br />
                    <h3>{{ msg }}</h3>
                </v-card-text>
                <v-card-text>
                    <v-row justify="end">
                        <v-btn
                            :disabled="$store.state.inProcess"
                            @click="preventSaveDialog = false"
                            outlined
                            tile
                            color="secondary"
                            class="mx-2"
                        >Cancelar</v-btn>
                        <v-btn
                            :loading="$store.state.inProcess"
                            :disabled="$store.state.inProcess"
                            @click="saveInventarios()"
                            tile
                            class="mx-2 elevation-0"
                            color="secondary"
                        >Aceptar</v-btn>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import ArticulosInventarios from "./ArticulosInventarios";
import ProveedoresForm from "../proveedores/ProveedoresForm.vue";

var cantidadMaxima = 999999999999999999;

export default {
    data: () => ({
        formPanel: false,
        inProcess: false,
        movimientos: [
            "ALTA",
            "INCREMENTO",
            "DEVOLUCION",
            "DECREMENTO",
            "MODIFICACION"
        ],
        disabled: {
            movimiento: false,
            proveedor: false
        },
        searchSupplier: null,
        searchSupplierTable: false,
        suppliers: [],
        msg: "",
        preventSaveDialog: false,
        proveedor: false,
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            cantidadMaxima: value =>
                (value <= cantidadMaxima && value > 0) ||
                "La cantidad no puede ser menor al lote existente"
        }
    }),

    computed: {
        cantidadLitros: {
            set() {},
            get() {
                if (this.$store.state.inventarios.form.cantidad) {
                    let cantidad = Number(
                        this.$store.state.inventarios.form.cantidad
                    );
                    let litros = Number(
                        this.$store.state.articulos.articulo.articulo.litros
                    );
                    return cantidad * litros;
                } else {
                    return null;
                }
            }
        }
    },

    components: {
        ArticulosInventarios,
        ProveedoresForm
    },

    mounted() {
        this.checkStock();
        this.searchSupplier = "GRUPO APC";
        this.$store.state.inventarios.form.supplier_id = 1;
    },

    methods: {
        checkStock() {
            if (this.$store.state.articulos.articulo.stock <= 0) {
                this.$store.state.inventarios.form.movimiento = "ALTA";
                this.disabled.movimiento = true;
                this.disabled.lote = true;
            } else {
                this.$store.state.inventarios.form.movimiento = "INCREMENTO";
                let find = this.movimientos.find(element => element == "ALTA");
                if (find) {
                    this.movimientos.splice(0, 1);
                }
                this.disabled.movimiento = false;
                this.disabled.lote = false;
            }

            this.movimientoOnChange();
        },

        movimientoOnChange() {
            if (this.$store.state.inventarios.form.movimiento) {
                let movimiento = this.$store.state.inventarios.form.movimiento;

                if (movimiento == "ALTA") {
                    this.disabled.proveedor = false;
                } else {
                    this.disabled.proveedor = true;
                }
            }
        },

        searchSupplierAfter() {
            this.inProcess = true;
            this.searchSupplierTable = true;
            if (this.searchSupplier != null && this.searchSupplier != "") {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {
                    this.findSupplier();
                }, 1000);
            }
        },

        findSupplier: async function() {
            this.$store.state.inventarios.form.supplier_id = null;
            this.$store
                .dispatch("proveedores/index", {
                    buscarProveedor: this.searchSupplier,
                    limit: 5
                })
                .then(response => {
                    this.suppliers = response.proveedores;
                    this.inProcess = false;
                });
        },

        selectSupplier(supplier) {
            this.searchSupplier = supplier.razonsocial;
            this.$store.state.inventarios.form.supplier_id = supplier.id;
            this.suppliers = [];
            this.searchSupplierTable = false;
        },

        preventSave() {
            if (this.$refs.inventariosForm.validate()) {
                let movimiento = this.$store.state.inventarios.form.movimiento;
                let cantidad = this.$store.state.inventarios.form.cantidad;

                if (movimiento == "ALTA" || movimiento == "INCREMENTO") {
                    this.msg = `Se agregaran ${cantidad} productos al stock`;
                } else if (movimiento == "MODIFICACION") {
                    this.msg = `Se modificará la cantidad ${cantidad} productos`;
                } else {
                    this.msg = `Se restarán ${cantidad} productos al stock`;
                }
                this.preventSaveDialog = true;
            }
        },
        saveInventarios: async function() {
            if (this.$refs.inventariosForm.validate()) {
                this.$store.state.inventarios.form.articulo_id = this.$store.state.articulos.articulo.articulo.id;
                this.$store.state.inventarios.form.cantidadlitros = this.cantidadLitros;
                await this.$store.dispatch("inventarios/save").catch(() => {
                    this.preventSaveDialog = false;
                });
                this.preventSaveDialog = false;
                this.formPanel = false;
                await this.$store.dispatch("articulos/show", {
                    id: this.$store.state.articulos.articulo.articulo.id
                });
                this.checkStock();
            }
        },

        cancelProveedores() {
            this.$refs.ProveedoresCreate.reset();
            this.proveedor = false;
        },

        saveProveedores: async function() {
            if (this.$refs.ProveedoresCreate.validate()) {
                await this.$refs.ProveedoresForm.getProvincia();
                await this.$refs.ProveedoresForm.getLocalidad();
                let proveedor = await this.$store.dispatch("proveedores/save");
                this.selectSupplier(proveedor);
                this.cancelProveedores();
            }
        }
    }
};
</script>

<style lang="scss"></style>
