<template>
    <div>
        <div v-if="$store.state.clientes.cliente.recibos.length > 0">
            <v-data-table
                :headers="headers"
                :items="$store.state.clientes.cliente.recibos"
                hide-default-footer
                :items-per-page="-1"
                :mobile-breakpoint="0"
            >
                <template v-slot:item="{item}">
                    <tr>
                        <td>{{item.numrecibo}}</td>
                        <td>{{item.fecha}}</td>
                        <td>{{item.total}}</td>
                        <td>
                            <v-menu offset-y>
                                <template v-slot:activator="{ on }">
                                    <v-btn color="secondary" text icon v-on="on">
                                        <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                    </v-btn>
                                </template>
                                <v-list>
                                    <v-list-item :to="`/clientes/showRecibos/${item.id}`">
                                        <v-list-item-title>Detalles</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="print(item.id)">
                                        <v-list-item-title>Imprimir</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </div>
        <div v-else>
            <br />
            <h2 class="text-center">El cliente no posee recibos</h2>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "Número", sortable: false },
            { text: "Fecha", sortable: false },
            { text: "Total", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    methods: {
        print(id) {
            this.$store.dispatch("PDF/printRecibo", { id: id });
        }
    }
};
</script>

<style>
</style>