<template>
    <div>
        <div v-if="$store.state.inProcess">
            <v-row justify="center" style="margin-top: 200px;">
                <v-progress-circular :size="70" :width="7" color="primary" indeterminate></v-progress-circular>
            </v-row>
        </div>
        <div v-else>
            <v-card v-if="$store.state.proveedores.proveedores" shaped>
                <v-card-title>Proveedores</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="$store.state.proveedores.proveedores.proveedores"
                        hide-default-footer
                        :items-per-page="limit"
                        :mobile-breakpoint="0"
                    >
                        <template v-slot:item="{item}">
                            <tr>
                                <td>{{item.cuit}}</td>
                                <td>{{item.razonsocial}}</td>
                                <td class="hidden-xs-only">{{item.telefono}}</td>
                                <td>
                                    <v-btn
                                        color="secondary"
                                        text
                                        icon
                                        :to="`/proveedores/show/${item.id}`"
                                    >
                                        <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                    <br />
                    <v-row justify="center">
                        <v-btn
                            :disabled="limit >= $store.state.proveedores.proveedores.total"
                            @click="loadMore()"
                            color="secondary"
                            outlined
                            tile
                        >Cargar Más</v-btn>
                    </v-row>
                    <br />
                </v-card-text>
            </v-card>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        limit: 10,
        headers: [
            { text: "CUIT", align: "left", sortable: false },
            { text: "Apellido y Nombre", sortable: false },
            { text: "Teléfono", sortable: false, class: "hidden-xs-only" },
            { text: "", sortable: false }
        ]
    }),

    mounted() {
        this.getProveedores();
    },

    methods: {
        getProveedores() {
            if (this.$store.state.auth.user.rol) {
                this.$store.dispatch("proveedores/index", {
                    limit: this.limit
                });
            }
        },

        loadMore() {
            this.limit += this.limit;
            this.getProveedores();
        }
    }
};
</script>

<style>
</style>