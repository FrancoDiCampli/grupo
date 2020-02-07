<template>
    <div>
        <v-tooltip left>
            <template v-slot:activator="{ on }">
                <v-btn color="secondary" dark fab fixed right bottom large v-on="on" to="/users">
                    <v-icon>fas fa-chevron-left</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card shaped>
                    <v-card-title>Nuevo usuario</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form ref="CreateUsers" @submit.prevent="saveUser()">
                                    <UsersForm mode="create"></UsersForm>
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
import UsersForm from "../../components/users/UsersForm";

export default {
    data: () => ({
        inProcess: false
    }),

    components: {
        UsersForm
    },

    methods: {
        saveUser() {
            if (this.$refs.CreateUsers.validate()) {
            }
        }
    }
};
</script>

<style>
</style>