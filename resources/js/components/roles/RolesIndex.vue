<template>
    <div>
        <v-card outlined shaped :loading="$store.state.inProcess">
            <v-card-title>Roles</v-card-title>
            <v-divider></v-divider>
            <v-card-text v-if="$store.state.roles.roles" class="px-0">
                <v-data-table
                    hide-default-footer
                    :headers="headers"
                    :items="$store.state.roles.roles.roles"
                    :items-per-page="limit"
                    :mobile-breakpoint="0"
                >
                    <template v-slot:item="item">
                        <tr>
                            <td>{{ item.item.role }}</td>
                            <td>
                                <v-menu offset-y>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="secondary" text icon v-on="on">
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item @click="setPermissions(item.item)">
                                            <v-list-item-title>Permisos</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="edit(item.item)">
                                            <v-list-item-title>Editar</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="item.item.id != 1"
                                            @click="beforeDelete(item.item.id)"
                                        >
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

        <!-- Dialog Permisos -->
        <v-dialog
            v-model="permissionsDialog"
            width="600"
            persistent
            :fullscreen="$vuetify.breakpoint.xsOnly"
            scrollable
        >
            <v-card>
                <v-card-title primary-title>Permisos</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-list dense>
                        <v-list-item two-line v-for="(permiso, index) in permisos" :key="index">
                            <v-list-item-content>
                                <v-list-item-title>{{permiso.descripcion}}</v-list-item-title>
                                <v-list-item-subtitle>{{permiso.permiso}}</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="secondary" text @click="permissionsDialog = false">Cerrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog delete -->
        <v-dialog v-model="deleteDialog" width="500" persistent>
            <v-card>
                <v-card-title primary-title>¿Estás seguro?</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <br />¿Estás seguro que deseas eliminar este Rol? este cambio es irreversible
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
                            @click="deleteRol()"
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
            { text: "Rol", sortable: false },
            { text: "", sortable: false }
        ],
        permissionsDialog: false,
        deleteDialog: false,
        permisos: [],
        rolID: null
    }),

    props: ["limit"],

    methods: {
        setPermissions(rol) {
            this.permisos = [];

            let permissions = rol.permission.split(" ");
            let descriptions = rol.description.split(", ");

            for (let i = 0; i < permissions.length; i++) {
                this.permisos.push({
                    permiso: permissions[i],
                    descripcion: descriptions[i]
                });
            }

            this.permissionsDialog = true;
        },

        edit(rol) {
            this.$store.dispatch("roles/edit", { data: rol });
            this.$router.push("roles/editar");
        },

        beforeDelete(id) {
            this.rolID = id;
            this.deleteDialog = true;
        },

        deleteRol: async function() {
            await this.$store.dispatch("roles/destroy", {
                id: this.rolID
            });
            await this.$store.dispatch("roles/index");
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