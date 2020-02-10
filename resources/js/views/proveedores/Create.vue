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

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card shaped outlined>
                    <v-card-title>Nuevo Proveedor</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form ref="CreateProveedores" @submit.prevent="saveProveedores()">
                                    <ProveedoresForm mode="create" ref="ProveedoresForm"></ProveedoresForm>
                                    <v-row justify="center">
                                        <v-btn
                                            type="submit"
                                            tile
                                            class="elevation-0"
                                            color="secondary"
                                            :disabled="$store.state.inProcess"
                                            :loading="$store.state.inProcess"
                                        >Guardar</v-btn>
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
        saveProveedores: async function() {
            if (this.$refs.CreateProveedores.validate()) {
                await this.$refs.ProveedoresForm.getProvincia();
                await this.$refs.ProveedoresForm.getLocalidad();
                await this.$store.dispatch("proveedores/save");
                this.$router.push("/proveedores");
            }
        }
    }
};
</script>

<style>
</style>