<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/pedidos">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form ref="CreatePedido" @submit.prevent="savePedido()">
                    <PedidosForm ref="formPedidos">
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
                    </PedidosForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import PedidosForm from "../../components/pedidos/PedidosForm";

export default {
    components: {
        PedidosForm
    },

    methods: {
        async savePedido() {
            if (this.$refs.CreatePedido.validate()) {
                let checkData = await this.$refs.formPedidos.setData();
                if (checkData) {
                    await this.$store.dispatch("pedidos/save");
                    this.$refs.formPedidos.getPoint();
                    this.$refs.formPedidos.getArticles();
                    this.resetForm();
                }
            }
        },

        resetForm() {
            this.$refs.CreatePedido.reset();
            this.$refs.formPedidos.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style></style>
