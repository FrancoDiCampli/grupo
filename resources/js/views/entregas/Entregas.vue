<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card shaped outlined :loading="$store.state.inProcess">
                        <v-card-title>Entregas</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text v-if="$store.state.entregas.entregas" class="px-0">
                            <v-data-table
                                :headers="headers"
                                :items="$store.state.entregas.entregas.entregas"
                                hide-default-footer
                                :items-per-page="-1"
                                :mobile-breakpoint="0"
                            >
                                <template v-slot:item="{ item }">
                                    <tr>
                                        <td>
                                            {{
                                                item.comprobanteadherido || item.numentrega
                                            }}
                                        </td>
                                        <td>{{ item.cliente.razonsocial }}</td>
                                        <td>{{ item.fecha | formatDate }}</td>
                                        <td>
                                            <v-menu offset-y>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn
                                                        color="secondary"
                                                        text
                                                        icon
                                                        v-on="on"
                                                    >
                                                        <v-icon size="medium"
                                                            >fas fa-ellipsis-v</v-icon
                                                        >
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        :to="`/entregas/show/${item.id}`"
                                                    >
                                                        <v-list-item-title
                                                            >Detalles</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item @click="print(item.id)">
                                                        <v-list-item-title
                                                            >Imprimir</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="checkRole()"
                                                        @click="openDeleteDialog(item.id)"
                                                    >
                                                        <v-list-item-title
                                                            >Eliminar</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </td>
                                    </tr>
                                </template>
                            </v-data-table>
                            <div v-if="$store.state.entregas.entregas">
                                <br />
                                <v-row justify="center" v-if="$store.state.entregas.entregas">
                                    <br />
                                    <v-btn
                                        :loading="$store.state.inProcess"
                                        :disabled="limit >= $store.state.entregas.entregas.total"
                                        @click="loadMore()"
                                        color="secondary"
                                        outlined
                                        tile
                                    >Cargar Más</v-btn>
                                </v-row>
                            </div>
                        </v-card-text>
                    </v-card>
                    <v-dialog v-model="deleteDialog" persistent width="450px">
                        <v-card v-if="inProcess">
                            <v-row justify="center">
                                <v-progress-circular
                                    :size="70"
                                    :width="7"
                                    color="primary"
                                    indeterminate
                                    style="margin: 32px 0 32px 0;"
                                ></v-progress-circular>
                            </v-row>
                        </v-card>
                        <v-card v-else>
                            <v-card-title class="headline">¿Estas seguro?</v-card-title>
                            <v-card-text>
                                Se eliminará la entrega seleccionada, esta acción es
                                irreversible. El stock sera restaurado.
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="error"
                                    text
                                    @click="cancelDelete()"
                                    :disabled="inProcess"
                                    >Cancelar</v-btn
                                >
                                <v-btn
                                    color="success"
                                    text
                                    @click="deleteEntrega()"
                                    :disabled="inProcess"
                                    >Aceptar</v-btn
                                >
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>

export default {
    data: () => ({
        inProcess: false,
        limit: 10,
        deleteDialog: false,
        deleteId: null,
        headers: [
            { text: "N°", sortable: false },
            { text: "Nombre/Apellido", sortable: false },
            { text: "Fecha", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    created() {
        window.addEventListener("scroll", this.loadOnScroll);
    },

    mounted() {
        this.getEntregas();
    },

    destroyed() {
        window.removeEventListener("scroll", this.loadOnScroll);
    },

    methods: {
        getEntregas: async function () {
            await this.$store.dispatch("entregas/index", {
                limit: this.limit,
            });
        },

        loadMore: async function () {
            this.limit += 10;
            await this.getEntregas();
        },

        loadOnScroll() {
            if(document.body.scrollTop + document.body.clientHeight >= document.body.scrollHeight) {
                if(!this.$store.state.inProcess) {
                    this.loadMore();
                }
            }
        },

        print(id) {
            this.$store.dispatch("PDF/printEntrega", { id: id });
        },

        openDeleteDialog(id) {
            this.deleteId = id;
            this.deleteDialog = true;
        },

        cancelDelete() {
            this.deleteId = null;
            this.deleteDialog = false;
        },

        async deleteEntrega() {
            this.inProcess = true;
            await this.$store.dispatch("entregas/destroy", {
                id: this.deleteId
            });
            this.inProcess = false;
            this.getEntregas();
            this.deleteDialog = false;
        },

        checkRole() {
            if(this.$store.state.auth.user) {
                if(
                    this.$store.state.auth.user.rol =='superAdmin' || 
                    this.$store.state.auth.user.rol =='administrador'
                ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        },
    },
};
</script>

<style></style>