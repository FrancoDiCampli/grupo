<template>
    <div>
        <v-row justify="center">
            <v-col cols="12" sm="10" lg="8">
                <v-card outlined shaped :loading="$store.state.inProcess">
                    <v-card-text v-if="$store.state.users.users" class="px-0">
                        <v-data-table
                            hide-default-footer
                            :headers="headers"
                            :items="$store.state.users.users.users"
                            :items-per-page="limit"
                            :mobile-breakpoint="0"
                        >
                            <template v-slot:item="item">
                                <tr>
                                    <td class="hidden-sm-and-down">{{ item.item.name }}</td>
                                    <td>{{ item.item.email }}</td>
                                    <td>{{ item.item.rol }}</td>
                                    <td>
                                        <v-menu offset-y>
                                            <template v-slot:activator="{ on }">
                                                <v-btn color="secondary" text icon v-on="on">
                                                    <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                                </v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item>
                                                    <v-list-item-title>Editar</v-list-item-title>
                                                </v-list-item>
                                                <v-list-item>
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
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "Nombre", sortable: false, class: "hidden-sm-and-down" },
            { text: "Email", sortable: false },
            { text: "Rol", sortable: false },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"]
};
</script>

 