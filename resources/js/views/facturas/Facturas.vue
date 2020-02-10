<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <FacturasIndex></FacturasIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import FacturasIndex from "../../components/facturas/FacturasIndex";

export default {
    components: {
        FacturasIndex
    }
};
</script>

<style>
</style>