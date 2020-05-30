<template>
    <div>
        <v-container>
            <v-btn
                v-if="$store.state.auth.user.rol != 'cliente'"
                color="secondary"
                tile
                @click="modalCotizacion = true"
            >Cotizacion</v-btn>
            <v-tabs right slider-color="secondary" active-class="secondary--text">
                <v-tab v-if="$store.state.auth.user.rol != 'cliente'">Activas</v-tab>
                <v-tab>
                    <span v-if="$store.state.auth.user.rol != 'cliente'">Todas</span>
                    <span v-else>Cuentas</span>
                </v-tab>
                <v-tab>Recibos</v-tab>
                <!-- Cuentas Activas -->
                <v-tab-item
                    style="background: white !important;"
                    v-if="$store.state.auth.user.rol != 'cliente'"
                >
                    <ClientesPagos :divisa="divisa" @eventCurrency="consultarDivisa()"></ClientesPagos>
                </v-tab-item>
                <!-- Todas las Cuentas -->
                <v-tab-item style="background: white !important;">
                    <ClientesCuentas></ClientesCuentas>
                </v-tab-item>
                <!-- Recibos -->
                <v-tab-item style="background: white !important;">
                    <ClientesRecibos></ClientesRecibos>
                </v-tab-item>
            </v-tabs>
        </v-container>

        <!-- Cotizacion -->
        <v-dialog
            v-model="modalCotizacion"
            width="600"
            persistent
            no-click-animation
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <v-card>
                <v-card-title primary-title>Cotización Dólar</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="adding">
                    <v-row justify="center" class="my-5">
                        <v-progress-circular :size="70" :width="7" color="secondary" indeterminate></v-progress-circular>
                    </v-row>
                </v-card-text>
                <v-card-text>
                    <br />
                    <v-row justify="space-around">
                        <v-col cols="12" sm="6" class="py-0">
                            <v-text-field
                                v-model="divisa.cotizacion"
                                :rules="[rules.required]"
                                label="Cotizacion"
                                outlined
                                type="number"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6" class="py-0">
                            <v-dialog
                                ref="dialogCotizacion"
                                v-model="dialogCotizacion"
                                :return-value.sync="divisa.fechaCotizacion"
                                persistent
                                :width="
                                            $vuetify.breakpoint.xsOnly
                                                ? '100%'
                                                : '300px'
                                        "
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="divisa.fechaCotizacion"
                                        label="Fecha de la cotización"
                                        readonly
                                        outlined
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="divisa.fechaCotizacion"
                                    scrollable
                                    locale="es"
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="
                                                    $refs.dialogCotizacion.save(
                                                        divisa.fechaCotizacion
                                                    )
                                                "
                                    >Aceptar</v-btn>
                                </v-date-picker>
                            </v-dialog>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row justify="end">
                        <v-btn
                            color="secondary"
                            tile
                            outlined
                            @click="modalCotizacion = false"
                        >Cancelar</v-btn>
                        <v-btn
                            color="secondary"
                            tile
                            class="elevation-0 mx-3"
                            @click="setCurrency()"
                        >Guardar</v-btn>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import ClientesPagos from "./ClientesPagos";
import ClientesCuentas from "./ClientesCuentas";
import ClientesRecibos from "./ClientesRecibos";

export default {
    data() {
        return {
            modalCotizacion: false,
            dialogCotizacion: false,
            divisa: {},
            rules: {
                required: value => !!value || "Este campo es obligatorio"
            }
        };
    },

    components: {
        ClientesPagos,
        ClientesCuentas,
        ClientesRecibos
    },

    watch: {
        currency() {
            this.consultarDivisa();
        }
    },

    mounted: async function() {
        await this.consultarDivisa();
    },

    methods: {
        // Consultar el intercabio de divizas de la API
        consultarDivisa() {
            return new Promise(resolve => {
                axios
                    .get("/api/consultar")
                    .then(response => {
                        this.divisa = {
                            cotizacion: response.data.valor,
                            fechaCotizacion: response.data.fecha
                        };
                        resolve(response.data);
                    })
                    .catch(error => {
                        this.inProcess = false;
                        throw new Error(error);
                    });
            });
        },

        setCurrency: async function() {
            await axios
                .post("/api/setCotizacion", {
                    cotizacion: this.divisa.cotizacion,
                    fechaCotizacion: this.divisa.fechaCotizacion
                })
                .then(response => {
                    console.log(response.data);
                })
                .catch(error => {
                    console.log(error);
                });

            this.consultarDivisa();
            this.modalCotizacion = false;
        }
    }
};
</script>

<style></style>
