<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ClientesMiCuenta></ClientesMiCuenta>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ClientesMiCuenta from "../../components/clientes/ClientesMiCuenta";

export default {
    components: {
        ClientesMiCuenta
    }
};
</script>

<style></style>
