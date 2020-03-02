<template>
    <div>
        <v-row justify="space-around">
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.documentounico"
                    :rules="
                        mode != 'edit'
                            ? [rules.required, rules.du]
                            : [rules.required]
                    "
                    label="CUIT / CUIL"
                    required
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-select
                    v-model="$store.state.clientes.form.condicioniva"
                    :items="condiciones"
                    :rules="[rules.required]"
                    label="Condición frente al IVA"
                    required
                    outlined
                ></v-select>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.razonsocial"
                    :rules="[rules.required, rules.max]"
                    label="Apellido y Nombre"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-select
                    label="Tipo"
                    v-model="$store.state.clientes.form.tipo"
                    :rules="[rules.required]"
                    :items="[{text: 'Cliente', value: false}, {text: 'Distribuidor', value: true}]"
                    item-text="text"
                    item-value="value"
                    required
                    outlined
                ></v-select>
            </v-col>
            <v-col cols="12" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.email"
                    :rules="[rules.required]"
                    label="Email"
                    required
                    outlined
                    type="email"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.password"
                    :rules="mode != 'edit' ? [rules.required] : []"
                    :append-icon="
                        password_type ? 'fas fa-eye' : 'fas fa-eye-slash'
                    "
                    @click:append="password_type = !password_type"
                    :type="password_type ? 'text' : 'password'"
                    label="Contraseña"
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.confirm_password"
                    :rules="mode != 'edit' ? [rules.required] : []"
                    :append-icon="
                        password_type_confirm
                            ? 'fas fa-eye'
                            : 'fas fa-eye-slash'
                    "
                    @click:append="
                        password_type_confirm = !password_type_confirm
                    "
                    :type="password_type_confirm ? 'text' : 'password'"
                    label="Repetir contraseña"
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.telefono"
                    :rules="[
                        rules.required,
                        rules.minTelefono,
                        rules.maxTelefono
                    ]"
                    label="Teléfono"
                    required
                    outlined
                ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.direccion"
                    :rules="[rules.required, rules.max]"
                    label="Dirección"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <!-- Contactos -->
            <v-col cols="12" class="mb-8 py-0">
                <v-card outlined>
                    <v-simple-table>
                        <thead>
                            <tr>
                                <th class="text-xs-left">Nombre</th>
                                <th class="text-xs-left">TEL / CEL</th>
                                <th class="text-xs-left hidden-sm-and-down">Email</th>
                                <th class="text-xs-left hidden-sm-and-down">Cargo</th>
                                <th class="text-xs-left"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in contactos" :key="index">
                                <td>{{ item.nombre }}</td>
                                <td>{{ item.numero }}</td>
                                <td class="hidden-sm-and-down">{{ item.email }}</td>
                                <td class="hidden-sm-and-down">{{ item.cargo }}</td>
                                <td>
                                    <v-btn color="primary" text icon @click="deleteContact(item)">
                                        <v-icon size="medium">fas fa-times</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </tbody>
                    </v-simple-table>
                    <v-card-text>
                        <v-dialog v-model="contactDialog" width="500">
                            <v-card>
                                <v-card-title primary-title>Agregar contacto</v-card-title>
                                <v-divider></v-divider>
                                <v-card-text>
                                    <v-form ref="contactForm" @submit.prevent="addContact()">
                                        <br />
                                        <v-row justify="space-around">
                                            <v-col cols="12" class="py-0">
                                                <v-text-field
                                                    v-model="contactForm.nombre"
                                                    :rules="[rules.required]"
                                                    label="Apellido / Nombre"
                                                    required
                                                    outlined
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" class="py-0">
                                                <v-text-field
                                                    v-model="contactForm.cargo"
                                                    :rules="[rules.required]"
                                                    label="Cargo"
                                                    required
                                                    outlined
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" class="py-0">
                                                <v-text-field
                                                    v-model="contactForm.numero"
                                                    :rules="[
                                                        rules.required,
                                                        rules.minTelefono,
                                                        rules.maxTelefono
                                                    ]"
                                                    label="TEL / CEL"
                                                    type="number"
                                                    required
                                                    outlined
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" class="py-0">
                                                <v-text-field
                                                    v-model="contactForm.email"
                                                    :rules="[rules.required]"
                                                    label="Email"
                                                    required
                                                    outlined
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row justify="center">
                                            <v-btn
                                                @click="contactDialog = false"
                                                tile
                                                outlined
                                                class="mx-2"
                                                color="secondary"
                                            >Cancelar</v-btn>
                                            <v-btn
                                                type="submit"
                                                tile
                                                class="mx-2 elevation-0"
                                                color="secondary"
                                            >Guardar</v-btn>
                                        </v-row>
                                    </v-form>
                                </v-card-text>
                            </v-card>
                        </v-dialog>
                        <v-tooltip left>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    v-on="on"
                                    color="secondary"
                                    dark
                                    small
                                    absolute
                                    bottom
                                    right
                                    fab
                                    @click="contactDialog = true"
                                >
                                    <v-icon size="medium">fas fa-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Añadir contactos</span>
                        </v-tooltip>
                    </v-card-text>
                </v-card>
            </v-col>
            <!-- _________ -->
            <v-col cols="12" sm="4" class="py-0">
                <v-combobox
                    v-model="$store.state.clientes.form.provincia"
                    :items="provincias"
                    :rules="[rules.required]"
                    item-text="nombre"
                    item-value="id"
                    label="Provincia"
                    required
                    editable
                    outlined
                    return-object
                ></v-combobox>
            </v-col>
            <v-col cols="12" sm="4" class="py-0">
                <v-combobox
                    v-model="$store.state.clientes.form.localidad"
                    :items="localidades"
                    :rules="[rules.required]"
                    :disabled="localidades ? localidades.length == 0 : true"
                    item-text="nombre"
                    item-value="id"
                    label="Localidad"
                    required
                    editable
                    outlined
                    return-object
                ></v-combobox>
            </v-col>
            <v-col cols="12" sm="4" class="py-0">
                <v-text-field
                    v-model="$store.state.clientes.form.codigopostal"
                    :rules="
                        mode != 'edit'
                            ? [rules.required, rules.postal]
                            : [rules.required]
                    "
                    label="Código Postal"
                    required
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0">
                <v-textarea
                    v-model="$store.state.clientes.form.observaciones"
                    outlined
                    label="Observaciones"
                    no-resize
                ></v-textarea>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import Provincias from "../../utils/provincias";
import Localidades from "../../utils/localidades";

export default {
    data: () => ({
        password_type: false,
        password_type_confirm: false,
        condiciones: [
            "CONSUMIDOR FINAL",
            "EXENTO",
            "RESP. MONOTRIBUTO",
            "RESP. INSCRIPTO"
        ],
        contactos: [],
        contactosEliminados: [],
        contactDialog: false,
        contactForm: {
            nombre: null,
            numero: null,
            email: null,
            cargo: null
        },
        provincias: Provincias.provincias,
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            min: value =>
                (value && value.length >= 6) ||
                "Este campo debe contener al menos 6 digitos",
            max: value =>
                (value && value.length <= 190) ||
                "Este campo no puede contener más de 190 digitos",
            du: value =>
                (value && value.length < 12 && value.length > 10) ||
                "Este campo debe contener 11 digitos",
            postal: value =>
                (value && value.length < 5 && value.length > 3) ||
                "Este campo debe contener 4 digitos",
            minTelefono: value =>
                (value && value.length <= 13) ||
                "Este campo no puede contener más de 13 digitos",
            maxTelefono: value =>
                (value && value.length >= 6) ||
                "Este campo debe contener al menos 6 digitos"
        }
    }),

    props: ["mode"],

    computed: {
        localidades() {
            if (this.$store.state.clientes.form.provincia) {
                let data = Localidades.find(
                    localidad =>
                        localidad.id ===
                        this.$store.state.clientes.form.provincia.id
                );
                if (data) {
                    if (data.ciudades) {
                        return data.ciudades;
                    } else {
                        return [];
                    }
                }
            } else {
                return [];
            }
        }
    },

    created() {
        if (this.mode == "edit") {
            // Buscar la provincia
            let provincia = Provincias.provincias.find(
                provincia =>
                    provincia.nombre ==
                    this.$store.state.clientes.form.provincia
            );
            this.$store.state.clientes.form.provincia = provincia;

            // Buscar la ciudad
            let data = Localidades.find(
                localidad => localidad.id === provincia.id
            );

            let ciudad = data.ciudades.find(
                ciudad =>
                    ciudad.nombre == this.$store.state.clientes.form.localidad
            );
            this.$store.state.clientes.form.localidad = ciudad;

            // Establecer los contactos
            this.contactos = this.$store.state.clientes.form.contactos;
        }
    },

    methods: {
        addContact: async function() {
            if (this.$refs.contactForm.validate()) {
                let contacto = this.contactForm;
                this.contactos.push(contacto);
                this.$store.state.clientes.form.contactos = this.contactos;
                this.contactForm = {
                    nombre: null,
                    numero: null,
                    email: null,
                    cargo: null
                };

                this.$refs.contactForm.resetValidation();
            }
            this.contactDialog = false;
        },

        deleteContact(contacto) {
            if (this.mode == "edit") {
                this.contactosEliminados.push(contacto);
                this.$store.state.clientes.form.eliminados = this.contactosEliminados;
            }

            let index = this.contactos.indexOf(contacto);
            this.contactos.splice(index, 1);
            this.$store.state.clientes.form.contactos = this.contactos;
        },

        getProvincia() {
            if (this.$store.state.clientes.form.provincia) {
                this.$store.state.clientes.form.provincia = this.$store.state.clientes.form.provincia.nombre;
            } else {
                this.$store.state.clientes.form.provincia = null;
            }
        },

        getLocalidad() {
            if (this.$store.state.clientes.form.localidad) {
                this.$store.state.clientes.form.localidad = this.$store.state.clientes.form.localidad.nombre;
            } else {
                this.$store.state.clientes.form.localidad = null;
            }
        }
    }
};
</script>

<style lang="scss"></style>
