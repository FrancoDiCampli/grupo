<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/ventas">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form ref="CreateVenta" @submit.prevent="saveVenta()">
                    <VentasForm ref="formVentas">
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
                    </VentasForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import VentasForm from "../../components/ventas/VentasForm";
export default {
    components: {
        VentasForm
    },
    methods: {
        async saveVenta() {
            if (this.$refs.CreateVenta.validate()) {
                let checkData = await this.$refs.formVentas.setData();
                if (checkData) {
                    await this.$store.dispatch("ventas/save");
                    this.$refs.formVentas.getPoint();
                    this.$refs.formVentas.getArticles();
                    this.resetForm();
                }
            }
        },
        resetForm() {
            this.$refs.CreateVenta.reset();
            this.$refs.formVentas.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style></style>