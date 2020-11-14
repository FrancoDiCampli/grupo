<template>
    <div>
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
                    <template v-slot:item="{item}">
                        <tr>
                            <td>{{item.comprobanteadherido || item.numentrega}}</td>
                            <td>{{item.cliente.razonsocial}}</td>
                            <td>{{item.fecha}}</td>
                            <td>
                                <v-menu offset-y>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="secondary" text icon v-on="on">
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item :to="`/entregas/show/${item.id}`">
                                            <v-list-item-title>Detalles</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                    <v-list>
                                        <v-list-item @click="deleteEntrega()">
                                            <v-list-item-title>Eliminar</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <slot></slot>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "N°", sortable: false },
            { text: "Nombre/Apellido", sortable: false },
            { text: "Fecha", sortable: false },
            { text: "", sortable: false },
        ],
    }),

    props: ["limit"],

    methods: {
        async deleteEntrega() {
            this.inProcess = true;
            await this.$store.dispatch("pedidos/destroy");
            this.inProcess = false;
        }
    }
};
</script>

<style>
</style>