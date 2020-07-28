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
                    to="/devoluciones"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form ref="CreateDevolucion" @submit.prevent="saveDevolucion()">
                    <DevolucionesForm ref="formDevolucion">
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
                    </DevolucionesForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import DevolucionesForm from "../../components/devoluciones/DevolucionesForm";

export default {
    components: {
        DevolucionesForm
    },

    methods: {
        async saveDevolucion() {
            if (this.$refs.CreateDevolucion.validate()) {
                let checkData = await this.$refs.formDevolucion.setData();
                if (checkData) {
                    await this.$store.dispatch("devoluciones/save");
                    this.$refs.formDevolucion.getPoint();
                    this.$refs.formDevolucion.getArticles();
                    this.resetForm();
                }
            }
        },

        resetForm() {
            this.$refs.CreateDevolucion.reset();
            this.$refs.formDevolucion.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style></style>
