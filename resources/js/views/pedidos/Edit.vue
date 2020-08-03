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
            <v-col cols="12" v-if="inProcess">
                <v-row justify="center">
                    <v-progress-circular
                        :size="70"
                        :width="7"
                        color="primary"
                        indeterminate
                        style="margin: 32px 0 32px 0;"
                    ></v-progress-circular>
                </v-row>
            </v-col>
            <v-col cols="12" md="10" lg="8" v-else>
                <v-form ref="EdidPedido" @submit.prevent="editPedido()">
                    <PedidosForm ref="formPedidos" mode="edit">
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
    props: ["id"],

    data: () => ({
        inProcess: true
    }),

    components: {
        PedidosForm
    },

    async mounted() {
        this.inProcess = true;
        await this.getPedido();
        this.inProcess = false;
    },

    methods: {
        getPedido() {
            this.$store.dispatch("pedidos/edit", { id: this.id });
        },

        async savePedido() {
            if (this.$refs.EdidPedido.validate()) {
                let checkData = await this.$refs.formPedidos.setData();
                if (checkData) {
                    await this.$store.dispatch("pedidos/update");
                    // this.$refs.formPedidos.getPoint();
                    // this.$refs.formPedidos.getArticles();
                    // this.resetForm();
                }
            }
        },

        resetForm() {
            this.$refs.EdidPedido.reset();
            this.$refs.formPedidos.resetData();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style></style>