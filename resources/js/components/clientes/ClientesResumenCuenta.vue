<template>
    <div>
        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-row>
                    <v-col cols="12" sm="6" class="py-0">
                        <v-dialog
                            ref="dialogFechaDesde"
                            v-model="dialogs.desde"
                            :return-value.sync="fechaDesde"
                            persistent
                            :width="
                                $vuetify.breakpoint.xsOnly ? '100%' : '300px'
                            "
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    :value="fechaDesde | formatDate"
                                    @input="value => (fechaDesde = value)"
                                    label="Fecha desde"
                                    readonly
                                    outlined
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="fechaDesde"
                                scrollable
                                locale="es"
                            >
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="
                                        $refs.dialogFechaDesde.save(fechaDesde)
                                    "
                                    >Aceptar</v-btn
                                >
                            </v-date-picker>
                        </v-dialog>
                    </v-col>
                    <v-col cols="12" sm="6" class="py-0">
                        <v-dialog
                            ref="dialogFechaHasta"
                            v-model="dialogs.hasta"
                            :return-value.sync="fechaHasta"
                            persistent
                            :width="
                                $vuetify.breakpoint.xsOnly ? '100%' : '300px'
                            "
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    :value="fechaHasta | formatDate"
                                    @input="value => (fechaHasta = value)"
                                    label="Fecha hasta"
                                    readonly
                                    outlined
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="fechaHasta"
                                scrollable
                                locale="es"
                            >
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="
                                        $refs.dialogFechaHasta.save(fechaHasta)
                                    "
                                    >Aceptar</v-btn
                                >
                            </v-date-picker>
                        </v-dialog>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <div v-if="cuenta" class="print-button" @click="print()">
                <v-icon>fas fa-print</v-icon>
            </div>
            <v-row>
                <v-col v-if="cuenta">
                    <v-row cols="12">
                        <v-col cols="6" class="text-center">
                            <b>DESDE:</b>
                            {{ cuenta.desde }}
                        </v-col>
                        <v-col cols="6" class="text-center">
                            <b>HASTA:</b>
                            {{ cuenta.hasta }}
                        </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-col cols="12" class="pre-body">
                        <p>
                            <b>CUIT:</b>
                            {{ cuenta.cliente.documentounico }}
                        </p>
                        <p>
                            <b>Razón Social:</b>
                            {{ cuenta.cliente.razonsocial }}
                        </p>
                        <p>
                            <b>Domicilio:</b>
                            {{ cuenta.cliente.direccion }}
                        </p>
                        <v-divider></v-divider>
                    </v-col>

                    <v-col cols="12">
                        <v-row>
                            <v-col cols="6" class="text-center">
                                <b>SALDO ANTERIOR:</b>
                                U$D {{ cuenta.saldoAnterior }}
                            </v-col>
                            <v-col cols="6" class="text-center">
                                <b>SALDO:</b>
                                U$D {{ cuenta.saldo }}
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <br />
                        <v-row>
                            <v-col cols="12" class="text-center">
                                <b>DEBE (-)</b>
                            </v-col>
                        </v-row>
                        <v-simple-table>
                            <thead>
                                <tr>
                                    <th class="text-xs-left">Fecha</th>
                                    <th class="text-xs-left">
                                        Movimiento
                                    </th>
                                    <th class="text-xs-left">
                                        Debe (-)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in cuenta.cuentas"
                                    :key="index"
                                >
                                    <td>{{ item.fecha }}</td>
                                    <td>{{ item.tipo }}</td>
                                    <td>U$D {{ item.importe }}</td>
                                </tr>
                            </tbody>
                        </v-simple-table>

                        <div class="text-right">
                            <b>Subtotal (U$D) {{ cuenta.debe }}</b>
                        </div>
                        <v-divider></v-divider>
                        <br />
                        <v-row>
                            <v-col cols="12" class="text-center">
                                <b>HABER (+)</b>
                            </v-col>
                        </v-row>
                        <v-simple-table>
                            <thead>
                                <tr>
                                    <th class="text-xs-left">Fecha</th>
                                    <th class="text-xs-left">
                                        Movimiento
                                    </th>
                                    <th class="text-xs-left">
                                        Haber (+)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in cuenta.pagos"
                                    :key="index"
                                >
                                    <td>{{ item.fecha }}</td>
                                    <td>
                                        {{ item.tipo }}
                                    </td>
                                    <td>U$D {{ item.importe }}</td>
                                </tr>
                            </tbody>
                        </v-simple-table>
                        <div class="text-right">
                            <b>Subtotal (U$D) {{ cuenta.haber }}</b>
                        </div>

                        <v-divider></v-divider>
                    </v-col>
                </v-col>
            </v-row>
        </v-row>
    </div>
</template>

<script>
import moment from "moment";

export default {
    data() {
        return {
            inProcess: false,
            recibo: null,
            dialogs: {
                desde: false,
                hasta: false
            },
            fechaDesde: moment().format("YYYY-MM-DD"),
            fechaHasta: moment().format("YYYY-MM-DD"),
            cuenta: null
        };
    },

    watch: {
        fechaDesde() {
            this.getResumenCuenta();
        },

        fechaHasta() {
            this.getResumenCuenta();
        }
    },

    methods: {
        getResumenCuenta: async function() {
            axios
                .post("/api/resumenCuenta", {
                    id: this.$store.state.clientes.cliente.cliente.id,
                    desde: this.fechaDesde,
                    hasta: this.fechaHasta
                })
                .then(response => {
                    this.cuenta = response.data;
                })
                .catch(error => {
                    console.log(error);
                });
        },

        print() {
            this.$store.dispatch("PDF/printResumenCuenta", {
                id: this.cuenta.cliente.id,
                desde: this.cuenta.desde,
                hasta: this.cuenta.hasta
            });
        }
    }
};
</script>

<style lang="scss"></style>
