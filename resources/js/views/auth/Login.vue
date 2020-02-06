<template>
    <div>
        <br />
        <v-row justify="center">
            <v-card
                max-width="400px"
                width="100%"
                min-height="250px"
                height="100%"
            >
                <v-card-title
                    class="primary white--text py-2 px-4"
                    primary-title
                >
                    Iniciar Sesión
                </v-card-title>
                <v-card-text>
                    <v-row
                        justify="center"
                        align="center"
                        style="min-height: 250px; height: 100%;"
                    >
                        <v-col cols="12" pa-3 v-if="$store.state.inProcess">
                            <v-row justify="center">
                                <v-progress-circular
                                    :size="70"
                                    :width="7"
                                    color="primary"
                                    indeterminate
                                ></v-progress-circular>
                            </v-row>
                        </v-col>
                        <v-col cols="12" v-else>
                            <v-form ref="loginForm" @submit.prevent="login()">
                                <LoginForm></LoginForm>
                                <v-row justify="space-between">
                                    <v-btn
                                        to="/register"
                                        text
                                        class="ml-5 elevation-0"
                                        color="primary"
                                        >Registrarse</v-btn
                                    >
                                    <v-btn
                                        type="submit"
                                        tile
                                        class="mr-5 elevation-0"
                                        color="primary"
                                        >Iniciar sesión</v-btn
                                    >
                                </v-row>
                            </v-form>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-row>
    </div>
</template>

<script>
// Components
import LoginForm from "../../components/auth/LoginForm.vue";

export default {
    components: {
        LoginForm
    },

    methods: {
        login: async function() {
            if (this.$refs.loginForm.validate()) {
                await this.$store.dispatch("auth/login");
                let user = await this.$store.dispatch("auth/user");
                user.permissions.push("authenticated");

                this.$user.set({
                    rol: user.rol,
                    permissions: user.permissions
                });

                this.$router.push("/welcome");
            }
        }
    }
};
</script>
