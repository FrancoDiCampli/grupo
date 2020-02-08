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
                    to="/proveedores"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
        <div v-if="inProcess">
            <v-row justify="center" style="margin-top: 200px;">
                <v-progress-circular :size="70" :width="7" color="primary" indeterminate></v-progress-circular>
            </v-row>
        </div>

        <v-row justify="center" v-else>
            <v-col cols="12" md="10" lg="8">
                <v-card shaped>
                    <v-card-title>Nuevo Proveedor</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form ref="CreateProveedores" @submit.prevent="saveProveedores()">
                                    <ProveedoresForm mode="create" ref="ProveedoresForm"></ProveedoresForm>
                                    <v-row justify="center">
                                        <v-btn type="submit" tile color="secondary">Guardar</v-btn>
                                    </v-row>
                                    <br />
                                </v-form>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import ProveedoresForm from "../../components/proveedores/ProveedoresForm";

export default {
    data: () => ({
        inProcess: false
    }),

    components: {
        ProveedoresForm
    },

    mounted() {
        this.$refs.CreateProveedores.reset();
    },

    methods: {
        saveProveedores: function() {
            if (this.$refs.CreateProveedores.validate()) {
                this.inProcess = true;
                this.$refs.ProveedoresForm.getProvincia();
                this.$refs.ProveedoresForm.getLocalidad();
                this.$store
                    .dispatch("proveedores/save")
                    .then(() => {
                        this.$store
                            .dispatch("proveedores/index")
                            .then(() => {
                                this.$router.push("/proveedores");
                            })
                            .catch(() => {
                                this.inProcess = false;
                            });
                    })
                    .catch(() => {
                        this.inProcess = false;
                    });
            }
        }
    }
};
</script>

<style>
</style>