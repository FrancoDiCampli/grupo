<template>
    <div>
        <v-card flat tile style="overflow-x: hidden;">
            <!-- Ver y editar Foto -->
            <v-row justify="center">
                <v-col cols="12">
                    <br />
                    <v-row justify="center">
                        <v-menu absolute>
                            <template v-slot:activator="{ on }">
                                <v-avatar
                                    v-on="on"
                                    size="160"
                                    color="primary"
                                    style="cursor: pointer;"
                                >
                                    <img
                                        v-if="
                                            $store.state.auth.user.user.foto != null
                                        "
                                        :src="$store.state.auth.user.user.foto"
                                        width="100%"
                                        height="auto"
                                    />
                                    <span
                                        v-else-if="$store.state.auth.user.user.name"
                                        class="white--text text-uppercase"
                                        style="font-size: 60px;"
                                    >
                                        {{
                                        $store.state.auth.user.user.name[0]
                                        }}
                                    </span>
                                </v-avatar>
                            </template>
                            <v-list>
                                <v-list-item @click="fotoDialog = true">
                                    <v-list-item-title>Subir foto</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-row>
                </v-col>
            </v-row>
            <!-- Ver y editar datos -->
            <v-row justify="center">
                <v-col cols="12" px-3 mt-5>
                    <v-form ref="editForm">
                        <v-list dense>
                            <v-subheader class="ml-2 primary--text">Tu nombre</v-subheader>
                            <v-list-item v-if="!editName">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{
                                        $store.state.auth.user.user.name
                                        }}
                                    </v-list-item-title>
                                </v-list-item-content>
                                <v-list-item-icon @click="editName = true" style="cursor: pointer;">
                                    <v-icon size="medium">fas fa-pen</v-icon>
                                </v-list-item-icon>
                            </v-list-item>
                            <v-list-item v-else style="margin-top: -15px;">
                                <v-list-item-content>
                                    <v-text-field
                                        v-model="$store.state.auth.form.name"
                                        :rules="[rules.required]"
                                        clearable
                                        clear-icon="fas fa-times"
                                        append-icon="fas fa-check"
                                        @click:append="edit()"
                                        single-line
                                    ></v-text-field>
                                </v-list-item-content>
                            </v-list-item>
                            <v-subheader class="ml-2 primary--text">Tu email</v-subheader>
                            <v-list-item v-if="!editEmail">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{
                                        $store.state.auth.user.user.email
                                        }}
                                    </v-list-item-title>
                                </v-list-item-content>
                                <v-list-item-icon
                                    @click="editEmail = true"
                                    style="cursor: pointer;"
                                >
                                    <v-icon size="medium">fas fa-pen</v-icon>
                                </v-list-item-icon>
                            </v-list-item>
                            <v-list-item v-else style="margin-top: -15px;">
                                <v-list-item-content>
                                    <v-text-field
                                        v-model="$store.state.auth.form.email"
                                        :rules="[rules.required]"
                                        clearable
                                        clear-icon="fas fa-times"
                                        append-icon="fas fa-check"
                                        @click:append="edit()"
                                        single-line
                                    ></v-text-field>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-form>
                </v-col>
                <v-col cols="12" mt-5>
                    <v-row justify="center">
                        <v-btn
                            @click="editPassDialog = true"
                            color="primary"
                            outlined
                        >Cambiar contraseña</v-btn>
                    </v-row>
                </v-col>
            </v-row>
        </v-card>
        <!-- Modal para subir una foto -->
        <v-dialog v-model="fotoDialog" persistent no-click-animation width="500">
            <v-card>
                <v-card-title class="primary px-2 pt-2 white--text">
                    <v-btn dark @click="fotoDialog = false" icon>
                        <v-icon size="medium">fas fa-times</v-icon>
                    </v-btn>
                    <div class="hidden-xs-only">Arrastra la imagen para ajustar</div>
                    <v-spacer></v-spacer>
                    <v-btn dark @click="foto.chooseFile()" icon style="margin-top: 2px;">
                        <v-icon size="medium">fas fa-image</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text style="padding: 50px;" class="croppa-account">
                    <v-row row align="center" class="foto-buttons">
                        <v-col cols="12" align-self="end" class="pa-0">
                            <v-btn
                                @click="foto.zoomIn()"
                                fab
                                small
                                dark
                                color="primary"
                                class="my-1"
                            >
                                <v-icon size="medium">fas fa-plus</v-icon>
                            </v-btn>
                        </v-col>
                        <v-col cols="12" align-self="start" class="pa-0">
                            <v-btn
                                @click="foto.zoomOut()"
                                fab
                                small
                                dark
                                color="primary"
                                class="my-1"
                            >
                                <v-icon size="medium">fas fa-minus</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-row justify="center">
                        <croppa
                            v-model="foto"
                            :width="300"
                            :height="300"
                            placeholder="Foto de Perfil"
                            placeholder-color="#000"
                            :placeholder-font-size="24"
                            canvas-color="transparent"
                            :show-remove-button="false"
                            :show-loading="true"
                            :loading-size="25"
                            :prevent-white-space="true"
                            :zoom-speed="10"
                        ></croppa>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="uploadFoto()"
                        :loading="$store.state.inProcess"
                        large
                        fab
                        right
                        dark
                        color="primary mb-5"
                        style="margin-top: -45px;"
                    >
                        <v-icon>fas fa-check</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Modal para editar la contraseña -->
        <v-dialog v-model="editPassDialog" persistent no-click-animation width="400">
            <v-card>
                <v-card-title class="primary px-2 pt-2 white--text">
                    <div class="ml-2">Cambiar contraseña</div>
                    <v-spacer></v-spacer>
                    <v-btn dark @click="editPassDialog = false" icon>
                        <v-icon size="medium">fas fa-times</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-form ref="passForm">
                        <v-row justify="center" style="margin-top: 40px;">
                            <v-col cols="12" class="py-0 px-3">
                                <v-text-field
                                    v-model="
                                        $store.state.auth.form.current_password
                                    "
                                    label="Contraseña actual"
                                    :rules="[rules.required]"
                                    :append-icon="
                                        currentPass
                                            ? 'fas fa-eye'
                                            : 'fas fa-eye-slash'
                                    "
                                    :type="currentPass ? 'text' : 'password'"
                                    @click:append="currentPass = !currentPass"
                                    outlined
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" class="py-0 px-3">
                                <v-text-field
                                    v-model="$store.state.auth.form.password"
                                    label="Contraseña nueva"
                                    :rules="[rules.required, rules.minLength]"
                                    :append-icon="
                                        newPass
                                            ? 'fas fa-eye'
                                            : 'fas fa-eye-slash'
                                    "
                                    :type="newPass ? 'text' : 'password'"
                                    @click:append="newPass = !newPass"
                                    outlined
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" class="py-0 px-3">
                                <v-text-field
                                    v-model="
                                        $store.state.auth.form.confirm_password
                                    "
                                    label="Confirmar contraseña"
                                    :rules="[rules.required, rules.minLength]"
                                    :append-icon="
                                        confirmPass
                                            ? 'fas fa-eye'
                                            : 'fas fa-eye-slash'
                                    "
                                    :type="confirmPass ? 'text' : 'password'"
                                    @click:append="confirmPass = !confirmPass"
                                    outlined
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        :loading="$store.state.inProcess"
                        color="primary"
                        text
                        @click="editPass()"
                    >Guardar cambios</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            foto: {},
            fotoDialog: false,
            editName: false,
            editEmail: false,
            editPassDialog: false,
            currentPass: false,
            newPass: false,
            confirmPass: false,
            rules: {
                required: value => !!value || "Este campo es obligatorio",
                minLength: value =>
                    (value && value.length >= 6) || "La contraseña es muy corta"
            }
        };
    },

    mounted() {
        if (JSON.parse(window.localStorage.getItem("logged"))) {
            this.setForm();
        }
    },

    methods: {
        setForm: async function() {
            let response = await this.$store.dispatch("auth/user");
            this.$store.commit("auth/fillForm", response.user);
        },

        uploadFoto: async function() {
            if (this.foto.hasImage()) {
                this.$store.state.auth.form.newFoto = this.foto.generateDataUrl();
                await this.$store.dispatch("auth/updateAccount");
                await this.$store.dispatch("auth/user");
                this.fotoDialog = false;
                this.foto.remove();
            }
        },

        edit: async function() {
            if (this.$refs.editForm.validate()) {
                await this.$store.dispatch("auth/updateAccount");
                await this.$store.dispatch("auth/user");
                this.editName = false;
                this.editEmail = false;
            }
        },

        editPass: async function() {
            if (this.$refs.passForm.validate()) {
                await this.$store.dispatch("auth/updateAccount");
                await this.$store.dispatch("auth/user");
                this.editPassDialog = false;
            }
        }
    }
};
</script>

<style lang="scss">
</style>