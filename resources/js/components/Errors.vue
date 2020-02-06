<template>
    <div v-if="$store.state.errors.length > 0">
        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-alert
                    color="error"
                    elevation="2"
                    border="left"
                    colored-border
                    v-for="(error, i) in $store.state.errors"
                    :key="i"
                >
                    <div v-if="error.data.message" class="ml-5">
                        <p class="error--text">
                            <b>Error {{ error.status }}:</b>
                            {{ error.data.message }}
                        </p>
                        <div v-for="(messages, k) in error.data.errors" :key="k">
                            <p v-for="(msg, j) in messages" :key="j">{{ msg }}</p>
                        </div>
                    </div>
                    <div v-else class="ml-5">
                        <p class="error--text">
                            <b>Error {{ error.status }}:</b>
                            {{ error.data }}
                        </p>
                    </div>
                    <v-row justify="end">
                        <v-btn
                            tile
                            class="mr-5 elevation-0"
                            color="error"
                            @click="
                                $store.commit('deleteError', error, {
                                    root: true
                                })
                            "
                        >Cerrar</v-btn>
                    </v-row>
                </v-alert>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {};
</script>

<style lang="scss"></style>
