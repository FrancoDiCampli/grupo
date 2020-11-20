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
            <v-col cols="10" md="8" lg="6">
                <v-card shaped outlined>
                    <v-card-title>Nuevo rol</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="center">
                            <v-col cols="12" sm="10">
                                <v-form ref="CreateRol" @submit.prevent="saveRol()">
                                    <RolesForm ref="RolesForm"></RolesForm>
                                    <v-row justify="center">
                                        <v-btn
                                            tile
                                            class="elevation-0"
                                            type="submit"
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
import RolesForm from "../../components/roles/RolesForm.vue";

export default {
    data: () => ({
        inProcess: false
    }),

    components: {
        RolesForm
    },

    mounted() {
        this.$refs.CreateRol.reset();
    },

    methods: {
        saveRol: async function() {
            if (this.$refs.CreateRol.reset()) {
                let permisos = await this.$store.dispatch("roles/permissions");
                let selected = this.$store.state.roles.form.scope;

                let formPermission = "";
                let formDescription = "";

                for (let i = 0; i < selected.length; i++) {
                    let find = permisos.find(
                        permiso => permiso.permission === selected[i]
                    );
                    if (find) {
                        formPermission = formPermission + find.permission + " ";
                        formDescription =
                            formDescription + find.description + ", ";
                    }
                }

                this.$store.state.roles.form.permission = formPermission;
                this.$store.state.roles.form.description = formDescription;
                await this.$store.dispatch("roles/save");
                this.updateSession();
                this.$router.push("/roles");
            }
        },

        updateSession() {
            this.$store
                .dispatch("auth/user")
                .then(response => {
                    response.permissions.push("authenticated");
                    this.$user.set({
                        rol: response.rol,
                        permissions: response.permissions
                    });
                    this.process = false;
                })
                .catch(error => {
                    this.process = false;
                });
        }
    }
};
</script>

<style>
</style>