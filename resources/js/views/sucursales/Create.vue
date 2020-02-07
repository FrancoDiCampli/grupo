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
                    to="/sucursales"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card shaped outlined>
                    <v-card-title>Nueva Sucursal</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form ref="CreateSucursal" @submit.prevent="saveSucursal()">
                                    <SucursalesForm mode="create" ref="SucursalesForm"></SucursalesForm>
                                    <v-row justify="center">
                                        <v-btn
                                            type="submit"
                                            tile
                                            class="elevation-0"
                                            :disabled="$store.state.inProcess"
                                            :loading="$store.state.inProcess"
                                            color="secondary"
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
import SucursalesForm from "../../components/sucursales/SucursalesForm";

export default {
    components: {
        SucursalesForm
    },

    mounted() {
        this.$refs.CreateSucursal.reset();
    },

    methods: {
        saveSucursal: async function() {
            if (this.$refs.CreateSucursal.validate()) {
                await this.$refs.SucursalesForm.getProvincia();
                await this.$refs.SucursalesForm.getLocalidad();
                await this.$store.dispatch("sucursales/save");
                this.$router.push("/sucursales");
            }
        }
    }
};
</script>

<style>
</style>