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
                    to="/presupuestos"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form
                    ref="CreatePresupuesto"
                    @submit.prevent="savePresupuesto()"
                >
                    <PresupuestosForm ref="formPresupuestos">
                        <v-btn
                            tile
                            outlined
                            color="secondary"
                            class="mx-2"
                            @click="resetForm()"
                            :disabled="$store.state.inProcess"
                            >Cancelar</v-btn
                        >

                        <v-btn
                            type="submit"
                            tile
                            color="secondary"
                            class="mx-2 elevation-0"
                            :disabled="$store.state.inProcess"
                            :loading="$store.state.inProcess"
                            >Guardar</v-btn
                        >
                    </PresupuestosForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import PresupuestosForm from "../../components/presupuestos/PresupuestosForm";

export default {
    components: {
        PresupuestosForm
    },

    methods: {
        async savePresupuesto() {
            if (this.$refs.CreatePresupuesto.validate()) {
                let checkData = await this.$refs.formPresupuestos.setData();
                if (checkData) {
                    await this.$store.dispatch("presupuestos/save");
                    this.$refs.formPresupuestos.getPoint();
                    this.$refs.formPresupuestos.getArticles();
                    this.resetForm();
                }
            }
        },

        resetForm() {
            this.$refs.CreatePresupuesto.reset();
            this.$refs.formPresupuestos.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style></style>
