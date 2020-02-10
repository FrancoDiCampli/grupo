<template>
    <div>
        <v-scroll-x-transition mode="out-in">
            <div v-if="!loginDialog" key="home">
                <v-row justify="center">
                    <img src="/img/logo.png" width="400" height="400" />
                </v-row>
                <v-row justify="center">
                    <v-btn
                        tile
                        outlined
                        color="primary"
                        class="hidden-xs-only mx-2"
                        href="http://www.grupoapc.com.ar/"
                    >grupoapc.com</v-btn>
                    <v-btn
                        tile
                        color="secondary"
                        class="elevation-0 mx-2"
                        @click="loginDialog = true"
                    >Iniciar sesión</v-btn>
                </v-row>
            </div>

            <div v-if="loginDialog" key="login">
                <v-row
                    justify="center"
                    align="center"
                    :class="$store.state.errors.length > 0 ? '' : 'login-height'"
                >
                    <v-card max-width="400px" width="100%" outlined>
                        <v-card-title primary-title>Iniciar Sesión</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row justify="center" align="center" style="height: 250px;">
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
                                        <v-row justify="end">
                                            <v-btn
                                                @click="loginDialog = false"
                                                text
                                                class="mr-5 elevation-0"
                                                color="secondary"
                                            >Cancelar</v-btn>
                                            <v-btn
                                                type="submit"
                                                tile
                                                class="mr-5 elevation-0"
                                                color="secondary"
                                            >Iniciar sesión</v-btn>
                                        </v-row>
                                    </v-form>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-row>
            </div>
        </v-scroll-x-transition>
    </div>
</template>

<script>
import LoginForm from "../components/auth/LoginForm.vue";

export default {
    data: () => ({
        loginDialog: false
    }),

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
            }
        }
    }
};
</script>

<style lang="scss">
.login-height {
    height: 90vh;
}
</style>
