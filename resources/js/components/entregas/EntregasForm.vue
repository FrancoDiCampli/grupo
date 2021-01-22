<template>
    <div>
        <v-card shaped outlined :loading="inProcess" class="pb-4">
            <v-card-title class="py-0 px-2">
                <v-row class="pa-0 ma-0">
                    <v-col cols="auto" align-self="center">Nueva entrega</v-col>
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
            <v-divider></v-divider>
                <v-card-text class="pa-8">
                    <v-row justify="space-around" class="my-1">
                        <!-- CLIENTE -->
                        <v-col cols="12" class="py-0">
                            <v-text-field
                                v-model="$store.state.entregas.form.cliente"
                                :rules="[rules.required]"
                                label="Cliente"
                                outlined
                                disabled
                            ></v-text-field>
                        </v-col>
                        <!-- FECHA -->
                        <v-col cols="12" sm="6" class="py-0">
                            <v-dialog
                                ref="dialogFecha"
                                v-model="fechaDialog"
                                :return-value.sync="
                                    $store.state.entregas.form.fecha
                                "
                                persistent
                                :width="
                                    $vuetify.breakpoint.xsOnly ? '100%' : '300px'
                                "
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        :value="
                                            $store.state.entregas.form.fecha
                                                | formatDate
                                        "
                                        @input="
                                            store
                                                ? value =>
                                                    (store.state.entregas.form.fecha = value)
                                                : value => null
                                        "
                                        label="Fecha"
                                        :rules="[rules.required]"
                                        readonly
                                        outlined
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="$store.state.entregas.form.fecha"
                                    scrollable
                                    locale="es"
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="
                                            $refs.dialogFecha.save(
                                                $store.state.entregas.form.fecha
                                            )
                                        "
                                        >Aceptar</v-btn
                                    >
                                </v-date-picker>
                            </v-dialog>
                        </v-col>
                        <!-- COMPROBANTE ADHERIDO -->
                        <v-col cols="12" sm="6" class="py-0">
                            <v-text-field
                                v-model="
                                    $store.state.entregas.form.comprobanteadherido
                                "
                                label="Entrega adherida Nº"
                                outlined
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row justify="space-around" class="my-1">
                        <!-- TABLA DETALLES -->
                        <v-col cols="12" class="py-0 mb-5">
                            <v-card outlined>
                                <v-simple-table :key="detailTableKey">
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th class="text-left">Articulo</th>
                                                <th class="text-left">
                                                    Presentación
                                                </th>
                                                <th class="text-left">Unidades</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(detalle, index) in detalles"
                                                :key="index"
                                            >
                                                <td>{{ detalle.articulo }}</td>
                                                <td>{{ detalle.litros }} L.</td>
                                                <td>
                                                    {{
                                                        detalle.entregando ||
                                                            detalle.cantidad -
                                                                detalle.cantidadentregado
                                                    }}
                                                </td>
                                                <td class="px-0">
                                                    <v-btn
                                                        icon
                                                        color="secondary"
                                                        @click="
                                                            openEditDetailDialog(
                                                                detalle
                                                            )
                                                        "
                                                    >
                                                        <v-icon size="medium"
                                                            >fas fa-pen</v-icon
                                                        >
                                                    </v-btn>
                                                </td>
                                                <td>
                                                    <v-btn
                                                        icon
                                                        color="secondary"
                                                        @click="
                                                            deleteDetail(detalle)
                                                        "
                                                    >
                                                        <v-icon size="medium"
                                                            >fas fa-times</v-icon
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

                    <v-row justify="center" class="my-1">
                        <v-col cols="12" class="py-0">
                            <v-textarea
                                v-model="$store.state.entregas.form.observaciones"
                                outlined
                                label="Observaciones"
                                no-resize
                            ></v-textarea>
                        </v-col>
                        <slot></slot>
                    </v-row>
                </v-card-text>
        </v-card>

        <v-dialog v-model="editDetailDialog" width="500" persistent>
            <v-card>
                <v-card-title>Editar detalle</v-card-title>
                <v-divider></v-divider>
                <br />
                <v-card-text>
                    <v-form @submit.prevent="editDetail" ref="editDetailDialogForm">
                        <v-row justify="center">
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.articulo"
                                    label="Articulo"
                                    outlined
                                    disabled
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="selectedDetail.entregando"
                                    label="Unidades"
                                    outlined
                                    :rules="[
                                        rules.required,
                                        rules.entregaMaxima,
                                        rules.stockDisponible
                                    ]"
                                    @keyup="editDetailControl()"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <br />
                        <v-row justify="end">
                            <v-btn
                                text
                                color="error"
                                @click="editDetailDialog = false"
                                >Cancelar</v-btn
                            >
                            <div class="mx-2"></div>
                            <v-btn
                                text
                                class="elevation-0"
                                type="submit"
                                color="secondary"
                                >Guardar</v-btn
                            >
                            <div class="mx-2"></div>
                        </v-row>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from "moment";
import ClickOutside from "v-click-outside";
var entregaMaxima = 999999999;
var stockDisponible = 999999999;

export default {
    directives: {
        clickOutside: ClickOutside.directive
    },

    data: () => ({
        step: 1,
        // GENERAL
        inProcess: false,
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            entregaMaxima: value =>
                value <= Number(entregaMaxima) ||
                "No se puede entregar más unidades",
            stockDisponible: value =>
                value <= Number(stockDisponible) || "No hay suficiente stock"
        },
        // HEADER
        PuntoVenta: null,
        NumComprobante: null,
        // DETALLES
        detalles: [],
        detailTableKey: 1,
        selectedDetail: {},
        editDetailDialog: false,
        // SUBTOTAL
        fechaDialog: false
    }),

    created() {
        if (this.$store.state.entregas.form.detalles) {
            let detallesForm = this.$store.state.entregas.form.detalles;
            for (let i = 0; i < detallesForm.length; i++) {
                if (
                    detallesForm[i].disponible >=
                    detallesForm[i].cantidad - detallesForm[i].cantidadentregado
                ) {
                    this.detalles.push(detallesForm[i]);
                }
            }
        } else {
            this.$router.push("/remitos");
        }
    },

    mounted: async function() {
        this.inProcess = true;
        await this.getPoint();
        this.inProcess = false;
    },

    methods: {
        // HEADER
        getPoint: async function() {
            let data;
            await axios.get("/api/config").then(response => {
                data = response.data;
            });
            this.PuntoVenta = data.puntoventa;
            let response = await this.$store.dispatch("entregas/index");
            if (response.ultima) {
                this.NumComprobante = Number(response.ultima.numentrega) + 1;
            } else {
                this.NumComprobante = data.numentrega;
            }
        },

        // DETALLES
        openEditDetailDialog(detail) {
            entregaMaxima = detail.cantidad - detail.cantidadentregado;
            stockDisponible = detail.disponible;
            let presentacion = detail.cantidadLitros / detail.cantidad;
            let entregando = detail.cantidad - detail.cantidadentregado
            this.selectedDetail = Object.assign({}, detail);
            this.selectedDetail.entregando = entregando;
            this.selectedDetail.presentacion = presentacion;
            this.editDetailDialog = true;
        },

        editDetailControl() {
            this.selectedDetail.cantidadLitros =
                this.selectedDetail.entregando * this.selectedDetail.presentacion;
        },

        editDetail() {
            if(this.$refs.editDetailDialogForm.validate()) {
                let index = this.detalles.indexOf(
                    this.detalles.find(
                        element => element.id == this.selectedDetail.id
                    )
                );

                this.detalles[index] = this.selectedDetail;
                this.detalles[index].entregando = this.selectedDetail.entregando;
                this.selectedDetail = {};
                this.editDetailDialog = false;
                this.detailTableKey += 1;
            }
            
        },

        deleteDetail(detalle) {
            let index = this.detalles.indexOf(detalle);
            this.detalles.splice(index, 1);
        },

        // FORM
        setData() {
            this.$store.state.entregas.form.detalles = this.detalles;
            return true;
        },

        resetForm() {
            this.detalles = [];
            this.selectedDetail = {};
        }
    }
};
</script>

<style lang="scss">
.btn-td {
    cursor: pointer;
}
</style>
