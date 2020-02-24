<template>
    <div>
        <v-tabs right hide-slider background-color="transparent">
            <v-spacer></v-spacer>
            <v-tab>Cartera</v-tab>
            <v-tab>Calendario</v-tab>
            <v-tab-item>
                <v-card shaped outlined :loading="$store.state.inProcess">
                    <v-card-title>Cartera</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text v-if="$store.state.reportes.cheques" class="px-0">
                        <v-data-table
                            :headers="headers"
                            :items="$store.state.reportes.cheques.cheques"
                            hide-default-footer
                            :items-per-page="limit"
                            :mobile-breakpoint="0"
                        >
                            <template v-slot:item="{ item }">
                                <tr>
                                    <td>{{ item.numero }}</td>
                                    <td>{{ item.dolares }}</td>
                                    <td>{{ item.estado }}</td>
                                    <td class="hidden-xs-only">
                                        <div
                                            v-show="item.estado == 'POR COBRAR'"
                                        >{{ daysDiff(item.fechacobro) }}</div>
                                    </td>
                                    <td>
                                        <v-menu offset-y>
                                            <template v-slot:activator="{ on }">
                                                <v-btn color="secondary" text icon v-on="on">
                                                    <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                                </v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item @click="showCheque(item)">
                                                    <v-list-item-title>Detalles</v-list-item-title>
                                                </v-list-item>
                                                <!-- <v-list-item>
                                                <v-list-item-title>Cobrar</v-list-item-title>
                                                </v-list-item>-->
                                            </v-list>
                                        </v-menu>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                        <slot></slot>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card shaped class="elevation-0" :loading="$store.state.inProcess">
                    <v-card-text>
                        <v-row justify="space-between">
                            <v-btn icon class="ma-2" @click="$refs.calendar.prev()">
                                <v-icon>fas fa-chevron-left</v-icon>
                            </v-btn>
                            <div class="mt-3">
                                <h2>{{ latinDate }}</h2>
                            </div>
                            <v-btn icon class="ma-2" @click="$refs.calendar.next()">
                                <v-icon>fas fa-chevron-right</v-icon>
                            </v-btn>
                        </v-row>
                        <v-calendar
                            v-model="calendarValue"
                            color="secondary"
                            ref="calendar"
                            type="month"
                            :events="events"
                            @change="getEvents"
                            :event-color="getEventColor"
                        ></v-calendar>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>

        <!-- CHEQUE DETALLES -->
        <v-dialog
            v-model="chequeDialog"
            width="600"
            persistent
            no-click-animation
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <v-card v-if="cheque">
                <v-card-text>
                    <v-row justify="space-between">
                        <div class="mt-6">
                            <h2 primary-title>Detalles</h2>
                        </div>
                        <div class="mt-4">
                            <v-btn class="mb-0 pb-0" icon @click="chequeDialog = false">
                                <v-icon>fas fa-times</v-icon>
                            </v-btn>
                        </div>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                    <br />

                    <v-row justify="space-around">
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Monto en dolares:</b>
                            </p>
                            <p>{{ cheque.dolares }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>cotización:</b>
                            </p>
                            <p>{{ cheque.cotizacion }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Monto en pesos:</b>
                            </p>
                            <p>{{ cheque.pesos }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Fecha de cotización:</b>
                            </p>
                            <p>{{ cheque.fecha_cotizacion }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Número:</b>
                            </p>
                            <p>{{ cheque.numero }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Banco:</b>
                            </p>
                            <p>{{ cheque.banco }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Recibido:</b>
                            </p>
                            <p>{{ cheque.fecharecibido }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Diferido:</b>
                            </p>
                            <p>{{ cheque.fechacobro }}</p>
                        </v-col>
                        <v-col cols="6" class="py-0">
                            <p>
                                <b>CUIT:</b>
                            </p>
                            <p>{{ cheque.cuit }}</p>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                            <p>
                                <b>Emisor:</b>
                            </p>
                            <p>{{ cheque.emisor }}</p>
                        </v-col>

                        <v-col cols="12" class="py-0" v-if="cheque.observaciones">
                            <p>
                                <b>Observaciones:</b>
                            </p>
                            <p>{{ cheque.observaciones }}</p>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment, { now } from "moment";

export default {
    data: () => ({
        headers: [
            { text: "Número", sortable: false },
            { text: "Monto", sortable: false },
            { text: "Estado", sortable: false },
            { text: "Cobrar", sortable: false, class: "hidden-xs-only" },
            { text: "", sortable: false }
        ],
        cheque: null,
        chequeDialog: false,
        calendarValue: moment().format("YYYY-MM-DD"),
        events: []
    }),

    props: ["limit"],

    computed: {
        latinDate() {
            return moment(this.calendarValue)
                .locale("es")
                .format("MMMM YYYY");
        }
    },

    methods: {
        daysDiff(endDate) {
            let today = moment()
                .format("YYYY-MM-DD")
                .toString();
            let start = moment(today, "YYYY-MM-DD");
            let end = moment(endDate, "YYYY-MM-DD");
            let diff = moment.duration(start.diff(end)).asDays() * -1;

            if (diff > 0) {
                return `En ${diff} Días`;
            } else if (diff < 0) {
                return `Hace ${diff * -1} Días`;
            } else {
                return "Hoy";
            }
        },

        showCheque(cheque) {
            this.cheque = cheque;
            this.chequeDialog = true;
        },

        getEvents() {
            if (this.$store.state.reportes.cheques) {
                this.events = [];
                let cheques = this.$store.state.reportes.cheques.cheques;
                for (const cheque in cheques) {
                    if (cheques.hasOwnProperty(cheque)) {
                        const element = cheques[cheque];
                        this.events.push({
                            name: element.numero,
                            start: element.fechacobro,
                            color:
                                element.estado == "POR COBRAR" ? "red" : "green"
                        });
                    }
                }
            }
        },

        getEventColor(event) {
            return event.color;
        }
    }
};
</script>

<style>
</style>
