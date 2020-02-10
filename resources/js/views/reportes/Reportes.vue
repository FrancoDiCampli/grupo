<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <ReportesIndex></ReportesIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import ReportesIndex from "../../components/reportes/ReportesIndex.vue";

export default {
    components: {
        ReportesIndex
    }
};
</script>

<style>
</style>