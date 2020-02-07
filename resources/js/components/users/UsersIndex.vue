<template>
    <div>
        <v-card outlined shaped :loading="$store.state.inProcess">
            <v-card-title>Usuarios</v-card-title>
            <v-divider></v-divider>
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
                                <v-menu offset-y v-if="item.item.id != 1">
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="secondary" text icon v-on="on">
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item @click="edit(item.item)">
                                            <v-list-item-title>Editar</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="item.item.id != $store.state.auth.user.user.id"
                                            @click="beforeDelete(item.item.id)"
                                        >
                                            <v-list-item-title>Eliminar</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                                <div v-else></div>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <slot></slot>
            </v-card-text>
        </v-card>

        <v-dialog v-model="deleteDialog" width="500" persistent>
            <v-card>
                <v-card-title primary-title>¿Estás seguro?</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <br />¿Estás seguro que deseas eliminar este Usuario? este cambio es irreversible
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                    <br />
                    <v-row justify="end">
                        <v-btn
                            tile
                            @click="deleteDialog = false;"
                            :disabled="$store.state.inProcess"
                            outlined
                            color="error"
                            class="mx-2"
                        >Cancelar</v-btn>
                        <v-btn
                            tile
                            @click="deleteUser()"
                            :disabled="$store.state.inProcess"
                            :loading="$store.state.inProcess"
                            color="error"
                            class="elevation-0 mx-2"
                        >Eliminar</v-btn>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
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
        ],
        userID: null,
        deleteDialog: false
    }),

    props: ["limit"],

    methods: {
        edit(user) {
            this.$store.dispatch("users/edit", { data: user });
            this.$router.push("users/editar");
        },

        beforeDelete(id) {
            this.userID = id;
            this.deleteDialog = true;
        },

        deleteUser: async function() {
            await this.$store.dispatch("users/destroy", {
                id: this.userID
            });
            await this.$store.dispatch("users/index");
            this.deleteDialog = false;
            this.updateSession();
        },

        updateSession() {
            this.$store
                .dispatch("auth/user")
                .then(response => {
                    response.permissions.push("authenticated");
                    this.$user.set({
                        rol: response.rol,
                        permissions: response.permissions
                    });
                    this.process = false;
                })
                .catch(error => {
                    this.process = false;
                });
        }
    }
};
</script>

 