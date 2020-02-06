<template>
    <v-app>
        <AppBar>
            <v-list-item @click="exit()">
                <v-list-item-title>Cerrar sesión</v-list-item-title>
            </v-list-item>
        </AppBar>
        <v-content>
            <v-container>
                <v-row justify="center" v-if="process">
                    <v-progress-circular
                        :size="70"
                        :width="7"
                        color="primary"
                        indeterminate
                        style="margin-top: 250px;"
                    ></v-progress-circular>
                </v-row>
                <div v-else>
                    <Errors></Errors>
                    <router-view></router-view>
                </div>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import AppBar from "./components/AppBar";
import Errors from "./components/Errors";

export default {
    data: () => ({
        process: false
    }),

    components: {
        AppBar,
        Errors
    },

    mounted() {
        if (JSON.parse(window.localStorage.getItem("logged"))) {
            this.recoverSession();
        }
    },

    methods: {
        recoverSession() {
            this.process = true;
            this.$store
                .dispatch("auth/user")
                .then(response => {
                    response.permissions.push("authenticated");
                    this.$user.set({
                        rol: response.rol,
                        permissions: response.permissions
                    });
                    this.$router.push("/welcome");
                    this.process = false;
                })
                .catch(error => {
                    this.process = false;
                });
        },

        exit: async function() {
            this.process = true;
            await this.$store.dispatch("auth/logout");
            this.$user.set({
                rol: "not_authorized",
                permissions: []
            });
            this.$router.push("/");
            this.process = false;
        }
    }
};
</script>

<style>
body::-webkit-scrollbar {
    width: 6px;
}
body::-webkit-scrollbar-thumb {
    background-color: #41b883;
}
</style>
