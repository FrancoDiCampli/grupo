<template>
    <div>
        <v-card
            v-if="$store.state.compras.compras"
            shaped
            outlined
            :loading="$store.state.inProcess"
        >
            <v-card-title>Compras</v-card-title>
            <v-divider></v-divider>
            <v-card-text class="px-0">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.compras.compras.remitos"
                    hide-default-footer
                    :items-per-page="-1"
                    :mobile-breakpoint="0"
                >
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>{{ item.numcompra }}</td>
                            <td>{{ item.proveedor.razonsocial }}</td>
                            <td class="hidden-xs-only">{{ item.fecha }}</td>
                            <td>{{ item.total }}</td>
                            <td>
                                <v-menu offset-y>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="secondary" text icon v-on="on">
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item :to="`/compras/show/${item.id}`">
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
                <slot></slot>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "Número", sortable: false },
            { text: "Proveedor", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-xs-only" },
            { text: "Importe", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"],

    methods: {
        print(id) {
            this.$store.dispatch("PDF/printCompra", { id: id });
        }
    }
};
</script>

<style></style>
