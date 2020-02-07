<template>
    <div>
        <v-btn color="secondary" dark fab fixed right bottom large to="roles/create">
            <v-icon>fas fa-plus</v-icon>
        </v-btn>
        <RolesIndex>
            <br />
            <v-row justify="center" v-if="$store.state.roles.roles">
                <v-btn
                    :disabled="limit >= $store.state.roles.roles.total"
                    :loading="$store.state.inProcess"
                    @click="loadMore()"
                    color="primary"
                    outlined
                    tile
                >Cargar Más</v-btn>
            </v-row>
        </RolesIndex>
    </div>
</template>

<script>
import RolesIndex from "../../components/roles/RolesIndex.vue";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        RolesIndex
    },

    mounted() {
        this.getRoles();
    },

    methods: {
        getRoles() {
            this.$store.dispatch("roles/index", {
                limit: this.limit
            });
        },

        loadMore() {
            this.limit += this.limit;
            this.getRoles();
        }
    }
};
</script>
