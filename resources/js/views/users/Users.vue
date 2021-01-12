<template>
    <div>
        <v-btn color="secondary" dark fab fixed right bottom large to="users/nuevo">
            <v-icon>fas fa-plus</v-icon>
        </v-btn>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
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
                </v-col>
            </v-row>
        </v-container>
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

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getUsers();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
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
        },

        loadOnScroll() {
            if(window.scrollY >= (document.body.clientHeight - window.innerHeight)) {
                this.loadMore();
            }
        },
    }
};
</script>