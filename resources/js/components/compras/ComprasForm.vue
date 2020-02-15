<template>
    <div>
        <!-- HEADER -->
        <div @click="focus = 'header'">
            <v-card
                shaped
                outlined
                class="form-header"
                :class="focus == 'header' ? 'elevation-3' : ''"
                :loading="inProcess"
            >
                <v-card-title class="py-0 px-2">
                    <v-row class="pa-0 ma-0">
                        <v-col cols="auto" align-self="center">Nueva Compra</v-col>
                        <v-spacer></v-spacer>
                        <v-col cols="auto">
                            <v-list-item two-line class="text-right">
                                <v-list-item-content>
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
            </v-card>
        </div>
    </div>
</template>

<script>
import ClickOutside from "vue-click-outside";

export default {
    data: () => ({
        // GENERAL
        focus: null,
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
        // HEADER
        PuntoVenta: null,
        // DIALOGS
        fechaDialog: false
    }),

    directives: {
        ClickOutside
    },

    async mounted() {
        this.inProcess = true;
        this.getPoint();
        this.inProcess = false;
    },

    methods: {
        // GENERAL
        nullFocus() {
            this.focus = null;
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
        }
    }
};
</script>

<style lang="scss">
</style>