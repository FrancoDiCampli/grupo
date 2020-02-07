<template>
    <div>
        <v-btn color="secondary" dark fab fixed right bottom large to="users/create">
            <v-icon>fas fa-plus</v-icon>
        </v-btn>
        <UsersIndex>
            <br />
            <v-row justify="center" v-if="$store.state.users.users">
                <v-btn
                    :disabled="limit >= $store.state.users.users.total"
                    :loading="$store.state.inProcess"
                    @click="loadMore()"
                    color="primary"
                    outlined
                    tile
                >Cargar Más</v-btn>
            </v-row>
        </UsersIndex>
    </div>
</template>

<script>
import UsersIndex from "../../components/users/UsersIndex.vue";

export default {
    data: () => ({
        limit: 10
    }),

    components: {
        UsersIndex
    },

    mounted() {
        this.getUsers();
    },

    methods: {
        getUsers() {
            this.$store.dispatch("users/index", {
                limit: this.limit
            });
        },

        loadMore() {
            this.limit += this.limit;
            this.getUsers();
        }
    }
};
</script>