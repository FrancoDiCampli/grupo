<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <RemitosIndex @erase="restart()">
                        <div v-if="$store.state.remitos.remitos">
                            <v-btn
                                :loading="$store.state.inProcess"
                                :disabled="limit >= $store.state.remitos.remitos.total"
                                @click="loadMore()"
                                color="secondary"
                                outlined
                                tile
                            >Cargar Más</v-btn>
                        </div>
                    </RemitosIndex>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import RemitosIndex from "../../components/remitos/RemitosIndex";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        RemitosIndex
    },

    mounted() {
        this.getRemitos();
    },

    methods: {
        getRemitos: async function() {
            await this.$store.dispatch("remitos/index", {
                limit: this.limit
            });
        },

        loadMore: async function() {
            this.limit += 10;
            await this.getRemitos();
        },

        restart() {
            this.getRemitos();
        }
    }
};
</script>

<style></style>
