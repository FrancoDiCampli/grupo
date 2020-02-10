<template>
    <div>
        <v-card shaped outlined :loading="$store.state.inProcess">
            <v-card-title>Proveedores</v-card-title>
            <v-divider></v-divider>
            <v-card-text v-if="$store.state.proveedores.proveedores" class="px-0">
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
                <slot></slot>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "CUIT", align: "left", sortable: false },
            { text: "Apellido y Nombre", sortable: false },
            { text: "Teléfono", sortable: false, class: "hidden-xs-only" },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"]
};
</script>

<style>
</style>