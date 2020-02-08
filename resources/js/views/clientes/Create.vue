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
                    to="/clientes"
                >
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card shaped outlined>
                    <v-card-title>Nuevo Cliente</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form
                                    ref="CreateClientes"
                                    @submit.prevent="saveClientes()"
                                >
                                    <ClientesForm
                                        mode="create"
                                        ref="ClientesForm"
                                    ></ClientesForm>
                                    <v-row justify="center">
                                        <v-btn
                                            type="submit"
                                            tile
                                            color="secondary"
                                            class="elevation-0"
                                            :disabled="$store.state.inProcess"
                                            :loading="$store.state.inProcess"
                                            >Guardar</v-btn
                                        >
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
import ClientesForm from "../../components/clientes/ClientesForm";

export default {
    components: {
        ClientesForm
    },

    mounted() {
        this.$refs.CreateClientes.reset();
    },

    methods: {
        saveClientes: async function() {
            if (this.$refs.CreateClientes.validate()) {
                await this.$refs.ClientesForm.getLocalidad();
                await this.$refs.ClientesForm.getProvincia();
                await this.$store.dispatch("clientes/save");
                this.$router.push("/clientes");
            }
        }
    }
};
</script>

<style></style>
