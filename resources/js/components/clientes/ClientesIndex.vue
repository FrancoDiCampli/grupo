<template>
    <div>
        <v-card shaped outlined :loading="$store.state.inProcess">
            <v-card-title>Clientes</v-card-title>
            <v-divider></v-divider>
            <v-card-text class="px-2" v-if="$store.state.clientes.clientes">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.clientes.clientes.clientes"
                    hide-default-footer
                    :items-per-page="limit"
                    :mobile-breakpoint="0"
                >
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>{{ item.documentounico }}</td>
                            <td>{{ item.razonsocial }}</td>
                            <td class="hidden-xs-only">{{ item.condicioniva }}</td>
                            <td v-if="item.id != 1">
                                <v-btn
                                    color="secondary"
                                    text
                                    icon
                                    :to="`/clientes/show/${item.id}`"
                                >
                                    <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                </v-btn>
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
            {
                text: "CUIT/CUIL",
                align: "left",
                sortable: false
            },
            { text: "Apellido y Nombre", sortable: false },
            { text: "Condicion IVA", sortable: false, class: "hidden-xs-only" },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"]
};
</script>

<style></style>
