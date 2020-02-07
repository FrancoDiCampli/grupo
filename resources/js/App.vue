<template>
    <v-app :style="{background: $vuetify.theme.themes[theme].background}">
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

    computed: {
        theme() {
            return this.$vuetify.theme.dark ? "dark" : "light";
        }
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
                    this.process = false;
                })
                .catch(error => {
                    this.process = false;
                });
        },

        exit: async function() {
            this.process = true;
            await this.$store.dispatch("auth/logout");
            this.$router.push("/"); // BORRAR CUANDO SE DEFINAN LOS PERMISOS Y ROLES EN LAS RUTAS
            this.process = false;
        }
    }
};
</script>

<style lang="scss">
body::-webkit-scrollbar {
    width: 6px;
}
body::-webkit-scrollbar-thumb {
    background-color: #44c2f7;
}
</style>
