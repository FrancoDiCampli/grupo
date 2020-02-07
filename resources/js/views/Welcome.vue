<template>
    <div>
        <v-row justify="center">
            <img src="img/logo.png" width="400" height="400" />
        </v-row>
        <h1 class="text-center secondary--text">Bienvenido</h1>
        <br />
        <div v-if="$store.state.auth.user">
            <v-row justify="center" v-if="$store.state.auth.user.rol.role == 'cliente'">
                <v-btn
                    tile
                    color="secondary"
                    class="elevation-0 mx-2"
                    @click="$router.push('/clientes/micuenta');"
                >Mi cuenta</v-btn>
            </v-row>
        </div>
    </div>
</template>

<script>
export default {};
</script>
