<template>
    <div>
        <v-row justify="center">
            <v-card
                shaped
                outlined
                width="794"
                height="1123"
                :loading="$store.state.inProcess"
            >
                <v-card-text class="pa-0 black--text">
                    <div v-if="cuenta" class="print-button" @click="print()">
                        <v-icon>fas fa-print</v-icon>
                    </div>
                    <v-row>
                        <v-col cols="12">
                            <h2 class="text-center mb-3">
                                RESUMEN NEA APC SAS
                            </h2>
                            <v-divider></v-divider>
                        </v-col>
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
                                                Descripción
                                            </th>
                                            <th class="text-xs-left">
                                                Debe (-)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(item,
                                            index) in cuenta.cuentas"
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
                                                Descripción
                                            </th>
                                            <th class="text-xs-left">
                                                Haber (+)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(item,
                                            index) in cuenta.pagos"
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
                </v-card-text>
            </v-card>
        </v-row>
    </div>
</template>

<script>
export default {
    data() {
        return {
            inProcess: false,
            recibo: null
        };
    },

    props: ["cuenta"],

    methods: {
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
