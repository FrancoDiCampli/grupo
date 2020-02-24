<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn
                    color="secondary"
                    dark
                    fab
                    fixed
                    right
                    bottom
                    large
                    v-on="on"
                    to="/consignaciones"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form ref="CreateConsignacion" @submit.prevent="saveConsignacion()">
                    <ConsignacionesForm ref="formConsignacion">
                        <v-btn
                            tile
                            outlined
                            color="secondary"
                            class="mx-2"
                            @click="resetForm()"
                            :disabled="$store.state.inProcess"
                        >Cancelar</v-btn>
                        <v-btn
                            type="submit"
                            tile
                            color="secondary"
                            class="mx-2 elevation-0"
                            :disabled="$store.state.inProcess"
                            :loading="$store.state.inProcess"
                        >Guardar</v-btn>
                    </ConsignacionesForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ConsignacionesForm from "../../components/consignaciones/ConsignacionesForm";

export default {
    components: {
        ConsignacionesForm
    },

    methods: {
        async saveConsignacion() {
            if (this.$refs.CreateConsignacion.validate()) {
                let checkData = await this.$refs.formConsignacion.setData();
                if (checkData) {
                    await this.$store.dispatch("consignaciones/save");
                    console.log(this.$store.state.consignaciones.form);
                    this.$refs.formConsignacion.getPoint();
                    this.$refs.formConsignacion.getArticles();
                    this.resetForm();
                }
            }
        },

        resetForm() {
            this.$refs.CreateConsignacion.reset();
            this.$refs.formConsignacion.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style></style>
