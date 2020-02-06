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
                    Registrarse
                </v-card-title>
                <v-card-text>
                    <v-row align="center">
                        <v-col cols="12" pa-3 v-if="$store.state.inProcess">
                            <v-row justify="center" style="margin-top: 40px;">
                                <v-progress-circular
                                    :size="70"
                                    :width="7"
                                    color="primary"
                                    indeterminate
                                ></v-progress-circular>
                            </v-row>
                        </v-col>
                        <v-col cols="12" v-else>
                            <v-form
                                ref="registerForm"
                                @submit.prevent="register()"
                            >
                                <RegisterForm></RegisterForm>
                                <v-row justify="space-between">
                                    <v-btn
                                        to="/login"
                                        text
                                        class="ml-5 elevation-0"
                                        color="primary"
                                        >Inicar sesión</v-btn
                                    >
                                    <v-btn
                                        type="submit"
                                        tile
                                        class="mr-5 elevation-0"
                                        color="primary"
                                        >Registrarse</v-btn
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
import RegisterForm from "../../components/auth/RegisterForm.vue";

export default {
    name: "Register",

    components: {
        RegisterForm
    },

    methods: {
        register: async function() {
            if (this.$refs.registerForm.validate()) {
                await this.$store.dispatch("auth/register");
                console.log("Usuario Registrado");
                // await this.$store.dispatch("auth/login");
                // let userData = await this.$store.dispatch("auth/user");
                // if (userData.user.role_id != null) {
                //     this.$user.set({ role: userData.rol.role });
                // } else {
                //     this.$user.set({ role: "visitor" });
                // }
            }
        }
    }
};
</script>
