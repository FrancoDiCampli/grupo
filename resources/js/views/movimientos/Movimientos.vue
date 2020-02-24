<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <MovimientosIndex>
                        <template slot="filter">
                            <v-row>
                                <v-col cols="12" sm="6" class="py-0">
                                    <v-dialog
                                        ref="dialogFechaDesde"
                                        v-model="dialogs.desde"
                                        :return-value.sync="fechaDesde"
                                        persistent
                                        :width="
                                            $vuetify.breakpoint.xsOnly
                                                ? '100%'
                                                : '300px'
                                        "
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="fechaDesde"
                                                label="Fecha desde"
                                                readonly
                                                outlined
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="fechaDesde" scrollable locale="es">
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="
                                                    $refs.dialogFechaDesde.save(
                                                        fechaDesde
                                                    )
                                                "
                                            >Aceptar</v-btn>
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
                                            $vuetify.breakpoint.xsOnly
                                                ? '100%'
                                                : '300px'
                                        "
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="fechaHasta"
                                                label="Fecha hasta"
                                                readonly
                                                outlined
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="fechaHasta" scrollable locale="es">
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="
                                                    $refs.dialogFechaHasta.save(
                                                        fechaHasta
                                                    )
                                                "
                                            >Aceptar</v-btn>
                                        </v-date-picker>
                                    </v-dialog>
                                </v-col>
                            </v-row>
                        </template>
                    </MovimientosIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import moment from "moment";
import MovimientosIndex from "../../components/movimientos/MovimientosIndex";

export default {
    data: () => ({
        dialogs: {
            desde: false,
            hasta: false
        },
        fechaDesde: moment().format("YYYY-MM-DD"),
        fechaHasta: moment().format("YYYY-MM-DD")
    }),

    components: {
        MovimientosIndex
    },

    watch: {
        fechaDesde() {
            this.getMovimientos();
        },

        fechaHasta() {
            this.getMovimientos();
        }
    },

    mounted() {
        this.getMovimientos();
    },

    methods: {
        getMovimientos: async function() {
            let response = await this.$store.dispatch("movimientos/index", {
                desde: this.fechaDesde,
                hasta: this.fechaHasta
            });
        }
    }
};
</script>

<style></style>
