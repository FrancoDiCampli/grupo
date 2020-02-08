<template>
    <div>
        <v-row justify="space-between">
            <v-col cols="12" sm="6">
                <v-row justify="center">
                    <v-col cols="12">
                        <v-row justify="center">
                            <croppa
                                v-model="foto"
                                :width="$vuetify.breakpoint.xsOnly ? 300 : 250"
                                :height="$vuetify.breakpoint.xsOnly ? 300 : 250"
                                placeholder="Foto"
                                placeholder-color="#000"
                                :placeholder-font-size="24"
                                canvas-color="transparent"
                                :show-remove-button="false"
                                :show-loading="true"
                                :loading-size="25"
                                :prevent-white-space="true"
                                :zoom-speed="10"
                                :initial-image="
                                    $store.state.articulos.articulo
                                        ? $store.state.articulos.articulo
                                              .articulo.foto
                                        : ''
                                "
                            ></croppa>
                        </v-row>
                    </v-col>

                    <v-col cols="12">
                        <v-row justify="center">
                            <v-btn
                                text
                                icon
                                color="secondary"
                                @click="foto.zoomIn()"
                            >
                                <v-icon>fas fa-search-plus</v-icon>
                            </v-btn>
                            <v-btn
                                text
                                icon
                                color="secondary"
                                @click="foto.zoomOut()"
                            >
                                <v-icon>fas fa-search-minus</v-icon>
                            </v-btn>
                            <v-btn
                                text
                                icon
                                color="secondary"
                                @click="foto.rotate()"
                            >
                                <v-icon>fas fa-redo-alt</v-icon>
                            </v-btn>
                            <div v-if="foto != null">
                                <v-btn
                                    v-show="foto.hasImage()"
                                    text
                                    icon
                                    color="secondary"
                                    @click="foto.remove()"
                                >
                                    <v-icon>fas fa-times</v-icon>
                                </v-btn>
                                <v-btn
                                    v-show="!foto.hasImage()"
                                    text
                                    icon
                                    color="secondary"
                                    @click="foto.chooseFile()"
                                >
                                    <v-icon>fas fa-plus</v-icon>
                                </v-btn>
                            </div>
                        </v-row>
                    </v-col>
                </v-row>
            </v-col>

            <v-col cols="12" sm="6" class="mt-3">
                <v-row justify="center">
                    <v-col cols="12" class="py-0">
                        <v-text-field
                            v-model="$store.state.articulos.form.articulo"
                            :rules="[rules.required]"
                            label="Articulo"
                            outlined
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" class="py-0">
                        <v-textarea
                            v-model="$store.state.articulos.form.descripcion"
                            :rules="[rules.required, rules.max]"
                            label="Descripción"
                            outlined
                            rows="6"
                        ></v-textarea>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-row justify="space-around">
            <v-col cols="12" sm="4" class="py-0">
                <v-text-field
                    v-model="$store.state.articulos.form.precio"
                    :rules="[rules.required]"
                    label="Precio"
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="4" class="py-0">
                <v-select
                    v-model="$store.state.articulos.form.litros"
                    :items="presentaciones"
                    item-text="name"
                    item-value="value"
                    :rules="[rules.required]"
                    label="Presentación"
                    outlined
                ></v-select>
            </v-col>
            <v-col cols="12" sm="4" class="py-0">
                <v-text-field
                    v-model="$store.state.articulos.form.stockminimo"
                    :rules="[rules.required]"
                    label="Stock Minimo"
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" class="py-0" v-if="mode == 'create'">
                <v-text-field
                    v-model="$store.state.articulos.form.stockInicial"
                    label="Stock Inicial"
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0" v-if="mode == 'create'">
                <v-select
                    v-model="$store.state.articulos.form.negocio"
                    :disabled="!$store.state.articulos.form.stockInicial"
                    :items="sucursales"
                    item-text="nombre"
                    item-value="nombre"
                    label="Sucursal"
                    outlined
                ></v-select>
            </v-col>

            <v-col cols="12" sm="6" class="py-0">
                <v-combobox
                    v-model="$store.state.articulos.form.categoria"
                    :items="categorias"
                    item-text="categoria"
                    item-value="categoria"
                    :search-input.sync="searchCategoria"
                    :disabled="$store.state.inProcess"
                    label="Categoria"
                    outlined
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    No hay resultados que coincidan con "
                                    <strong>{{ searchCategoria }}</strong
                                    >". Presione <kbd>enter</kbd> enter para
                                    crear uno nuevo
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-combobox>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-combobox
                    v-model="$store.state.articulos.form.marca"
                    :items="marcas"
                    item-text="marca"
                    item-value="marca"
                    :search-input.sync="searchMarca"
                    :disabled="$store.state.inProcess"
                    label="Marca"
                    outlined
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    No hay resultados que coincidan con "
                                    <strong>{{ searchCategoria }}</strong
                                    >". Presione <kbd>enter</kbd> enter para
                                    crear uno nuevo
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-combobox>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    label="Medida"
                    v-model="medida"
                    outlined
                    disabled
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    label="Codigo"
                    v-model="codigo"
                    outlined
                    disabled
                ></v-text-field>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "ProductosForm",

    props: ["mode"],

    data() {
        return {
            medida: "Litros",
            searchCategoria: null,
            searchMarca: null,
            categorias: [],
            marcas: [],
            sucursales: [],
            presentaciones: [
                { name: "1 Litro", value: 1 },
                { name: "5 Litros", value: 5 },
                { name: "10 Litros", value: 10 },
                { name: "20 Litros", value: 20 },
                { name: "1000 Litros", value: 1000 }
            ],
            foto: null,
            articulosLastID: null,
            rules: {
                required: value => !!value || "Este campo es obligatorio",
                cod: value =>
                    (value && value.length == 13) ||
                    "Este campo debe contener si o si 13 digitos",
                max: value =>
                    (value && value.length <= 190) ||
                    "Este campo no puede contener mas de 190 digitos"
            }
        };
    },

    computed: {
        codigo: {
            set() {},
            get() {
                if (this.mode == "create") {
                    if (
                        this.$store.state.articulos.form.articulo &&
                        this.$store.state.articulos.form.litros
                    ) {
                        if (
                            this.$store.state.articulos.form.articulo.length >=
                            3
                        ) {
                            let codigo =
                                this.$store.state.articulos.form.articulo[0] +
                                this.$store.state.articulos.form.articulo[1] +
                                this.$store.state.articulos.form.articulo[2] +
                                this.$store.state.articulos.form.litros +
                                "L";
                            let newId = this.articulosLastID + 1;
                            let number = codigo.toString() + newId.toString();
                            let zeroLength = 13 - number.length;
                            for (let i = 0; i < zeroLength; i++) {
                                codigo += "0";
                            }
                            codigo += newId.toString();
                            this.$store.state.articulos.form.codarticulo = codigo.toUpperCase();
                            return codigo.toUpperCase();
                        } else {
                            this.$store.state.articulos.form.codarticulo = null;
                            return null;
                        }
                    } else {
                        this.$store.state.articulos.form.codarticulo = null;
                        return null;
                    }
                } else {
                    return this.$store.state.articulos.form.codarticulo;
                }
            }
        }
    },

    mounted() {
        if (this.$store.state.auth.user != null) {
            this.getLastId();
            this.getCategorias();
            this.getMarcas();
            this.getSucursales();
        }
    },

    methods: {
        getLastId() {
            if (this.$store.state.articulos.articulos != null) {
                if (this.$store.state.articulos.articulos.ultimo != null) {
                    this.articulosLastID = this.$store.state.articulos.articulos.ultimo.id;
                }
            } else {
                this.$store.dispatch("articulos/index").then(response => {
                    if (response.ultimo) {
                        this.articulosLastID = response.ultimo.id;
                    } else {
                        this.articulosLastID = 0;
                    }
                });
            }
        },

        getCategorias() {
            this.$store.dispatch("categorias/index").then(response => {
                this.categorias = response.categorias;
                this.$store.state.articulos.form.categoria =
                    response.categorias[0].categoria;
            });
        },

        getMarcas() {
            this.$store.dispatch("marcas/index").then(response => {
                this.marcas = response.marcas;
                this.$store.state.articulos.form.marca =
                    response.marcas[0].marca;
            });
        },

        getSucursales() {
            this.$store.dispatch("sucursales/index").then(response => {
                this.sucursales = response.negocios;
                this.$store.state.articulos.form.negocio =
                    response.negocios[0].nombre;
            });
        },

        getFoto() {
            if (this.foto) {
                return this.foto.generateDataUrl();
            }
        }
    }
};
</script>

<style>
input[type="number"] {
    -moz-appearance: textfield;
}
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
}
</style>
