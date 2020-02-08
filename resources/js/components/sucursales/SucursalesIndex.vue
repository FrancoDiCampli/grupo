<template>
    <div>
        <v-card shaped outlined :loading="$store.state.inProcess">
            <v-card-title>Sucursales</v-card-title>
            <v-divider></v-divider>
            <v-card-text class="px-0" v-if="$store.state.sucursales.sucursales">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.sucursales.sucursales.negocios"
                    hide-default-footer
                    :items-per-page="limit"
                    :mobile-breakpoint="0"
                >
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>{{ item.nombre }}</td>
                            <td>{{ item.direccion }}</td>
                            <td class="hidden-xs-only">{{ item.localidad }}</td>
                            <td class="hidden-xs-only">{{ item.provincia }}</td>
                            <td>
                                <v-btn
                                    color="secondary"
                                    text
                                    icon
                                    :to="`/sucursales/show/${item.id}`"
                                >
                                    <v-icon size="medium"
                                        >fas fa-ellipsis-v</v-icon
                                    >
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
            { text: "Nombre", sortable: false },
            { text: "Dirección", sortable: false },
            { text: "Localidad", sortable: false, class: "hidden-xs-only" },
            { text: "Provincia", sortable: false, class: "hidden-xs-only" },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"]
};
</script>

<style></style>
