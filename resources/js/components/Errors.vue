<template>
    <div v-if="$store.state.errors.length > 0">
        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-alert
                    color="error"
                    border="left"
                    colored-border
                    class="alert-border"
                    style="border: thin solid #e0e0e0; border-left: none;"
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
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    text
                                    class="mr-5 elevation-0"
                                    color="error"
                                    v-on="on"
                                    @click="notificate(error)"
                                >
                                    <v-icon size="medium" class="mr-3">fab fa-whatsapp</v-icon>Notificar
                                </v-btn>
                            </template>
                            <span>Notificar al desarrollador</span>
                        </v-tooltip>
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
export default {
    methods: {
        notificate(error) {
            let testString = `Error ${error.status}: \n${error.data.message}`;

            let errorString = `Error ${error.status}: `;
            if (error.data.message) {
                errorString = errorString + `${error.data.message}.\n`;

                for (const key in error.data.errors) {
                    if (error.data.errors.hasOwnProperty(key)) {
                        for (
                            let i = 0;
                            i < error.data.errors[key].length;
                            i++
                        ) {
                            errorString =
                                errorString + `\n${error.data.errors[key][i]}`;
                        }
                    }
                }
            } else {
                errorString = errorString + `${error.data}.`;
            }

            let errorURI = encodeURI(errorString);

            let randomNumber = Math.floor(Math.random() * 2);
            let phoneNumbers = ["5493735414420", "5493735527874"];

            window.open(
                `https://wa.me/${phoneNumbers[randomNumber]}?text=${errorURI}`,
                "_blank"
            );
        }
    }
};
</script>