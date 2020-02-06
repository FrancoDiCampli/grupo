<template>
    <div>
        <br />
        <br />
        <v-row>
            <v-col cols="12">
                <v-row justify="center">
                    <img src="img/logo.png" width="300" height="auto" />
                </v-row>
            </v-col>
            <v-col cols="12">
                <h1
                    class="display-2 font-weight-light secondary--text text-center"
                >
                    Controller Auth
                </h1>
            </v-col>
        </v-row>
        <br />
        <v-row justify="space-around" justify-sm="center">
            <v-col cols="auto">
                <v-btn outlined tile color="primary" to="/register"
                    >Crear cuenta</v-btn
                >
            </v-col>
            <v-col cols="auto">
                <v-btn tile class="elevation-0" color="primary" to="/login"
                    >Iniciar sesión</v-btn
                >
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {};
</script>
