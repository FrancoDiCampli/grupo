<template>
    <div>
        <v-card
            shaped
            outlined
            :loading="$store.state.inProcess"
            v-if="$store.state.devoluciones.devoluciones"
        >
            <v-card-title>Devoluciones</v-card-title>
            <v-divider></v-divider>
            <v-card-text class="px-0">
                <v-data-table
                    :headers="headers"
                    :items="$store.state.devoluciones.devoluciones.devoluciones"
                    hide-default-footer
                    :items-per-page="-1"
                    :mobile-breakpoint="0"
                >
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.dependencia.name }}</td>
                            <td class="hidden-xs-only">{{ item.fecha | formatDate }}</td>
                            <td>
                                <v-menu offset-y>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="secondary" text icon v-on="on">
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item
                                            :to="`/devoluciones/show/${item.id}`"
                                        >
                                            <v-list-item-title
                                                >Detalles</v-list-item-title
                                            >
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
            { text: "Nombre", sortable: false },
            { text: "Fecha", sortable: false, class: "hidden-xs-only" },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"],

    methods: {
        print(id) {
            this.$store.dispatch("PDF/printDevolucion", { id: id });
        }
    }
};
</script>

<style></style>
