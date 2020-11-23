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

        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ComprasShow :id="id"></ComprasShow>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ComprasShow from "../../components/compras/ComprasShow.vue";

export default {
    props: ["id"],

    components: {
        ComprasShow
    }
};
</script>

<style>
</style>