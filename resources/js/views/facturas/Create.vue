<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" @click="$router.go(-1)">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-form ref="CreateFactura" @submit.prevent="saveFactura()">
                    <FacturasForm ref="formFacturas">
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
                    </FacturasForm>
                </v-form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import FacturasForm from "../../components/facturas/FacturasForm";

export default {
    components: {
        FacturasForm,
    },

    methods: {
        async saveFactura() {
            if (this.$refs.CreateFactura.validate()) {
                let checkData = await this.$refs.formFacturas.setData();
                if (checkData) {
                    await this.$store.dispatch("facturas/save");
                    this.$refs.formFacturas.getPoint();
                    this.resetForm();
                }
            }
        },

        resetForm() {
            this.$router.push("/facturas");
        },
    },
};
</script>

<style></style>
