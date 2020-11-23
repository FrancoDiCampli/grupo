<template>
    <div>
        <div v-if="$store.state.auth.user">
            <v-row
                justify="center"
                align="center"
                class="cliente-layout"
                v-if="$store.state.auth.user.rol == 'cliente'"
            >
                <v-col
                    cols="12"
                    sm="4"
                    class="cliente-btn"
                    @click="$router.push('/clientes/micuenta');"
                >
                    <v-row justify="center">
                        <v-col cols="12">
                            <v-row justify="center">
                                <v-avatar size="200" color="secondary">
                                    <v-icon class="cliente-icon">fas fa-hand-holding-usd</v-icon>
                                </v-avatar>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <h1 class="secondary--text text-center">Mi cuenta</h1>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col
                    cols="12"
                    sm="4"
                    class="cliente-btn"
                    @click="exitTo('https://play.google.com/store/apps/details?id=com.grupoapc.app&hl=es')"
                >
                    <v-row justify="center">
                        <v-col cols="12">
                            <v-row justify="center">
                                <v-avatar size="200" color="secondary">
                                    <img src="/img/logo.png" style="margin-left: -12px;" />
                                </v-avatar>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <h1 class="secondary--text text-center">Aplicación oficial</h1>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col
                    cols="12"
                    sm="4"
                    class="cliente-btn"
                    @click="exitTo('http://grupoapc.com.ar/')"
                >
                    <v-row justify="center">
                        <v-col cols="12">
                            <v-row justify="center">
                                <v-avatar size="200" color="secondary">
                                    <v-icon class="cliente-icon">fas fa-globe</v-icon>
                                </v-avatar>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <h1 class="secondary--text text-center">Página oficial</h1>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <div v-else>
                <v-row justify="center">
                    <img src="img/logo.png" width="400" height="400" />
                </v-row>
                <h1 class="text-center secondary--text">Bienvenido</h1>
                <br />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        exitTo(route) {
            window.open(route);
        },
    }
};
</script>

<style lang="scss" scoped>
.cliente-layout {
    height: 80vh;
}

.cliente-btn {
    cursor: pointer;
}

.cliente-icon {
    color: white !important;
    font-size: 78px !important;
}
</style>