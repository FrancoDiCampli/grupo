<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/compras">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form ref="CreateCompra" @submit.prevent="saveCompra()">
                    <ComprasForm ref="formCompras">
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
                    </ComprasForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ComprasForm from "../../components/compras/ComprasForm";

export default {
    components: {
        ComprasForm
    },

    methods: {
        async saveCompra() {
            if (this.$refs.CreateCompra.validate()) {
                let checkData = await this.$refs.formCompras.setData();
                if (checkData) {
                    await this.$store.dispatch("compras/save");
                    this.$refs.formCompras.getPoint();
                    this.$refs.formCompras.getArticles();
                    this.resetForm();
                }
            }
        },

        resetForm() {
            this.$refs.CreateCompra.reset();
            this.$refs.formCompras.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style>
</style>