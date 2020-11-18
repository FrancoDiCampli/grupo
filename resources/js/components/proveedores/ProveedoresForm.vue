<template>
    <div>
        <v-row justify="space-around">
            <v-col cols="12" sm="4" class="py-0">
                <v-text-field
                    v-model="$store.state.proveedores.form.cuit"
                    :rules="mode != 'edit' ? [rules.required, rules.cuit] : [rules.required]"
                    label="CUIT"
                    required
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="8" class="py-0">
                <v-text-field
                    v-model="$store.state.proveedores.form.razonsocial"
                    :rules="[rules.required, rules.max]"
                    label="Apellido y nombre"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.proveedores.form.direccion"
                    :rules="[rules.required, rules.max]"
                    label="Dirección"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.proveedores.form.telefono"
                    :rules="[rules.required, rules.minTelefono, rules.maxTelefono]"
                    label="Teléfono"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="12" class="py-0">
                <v-text-field
                    v-model="$store.state.proveedores.form.email"
                    :rules="[rules.required]"
                    label="Email"
                    required
                    outlined
                    type="email"
                ></v-text-field>
            </v-col>
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
                            <tr v-for="(item,index) in contactos" :key="index">
                                <td>{{item.nombre}}</td>
                                <td>{{item.numero}}</td>
                                <td class="hidden-sm-and-down">{{item.email}}</td>
                                <td class="hidden-sm-and-down">{{item.cargo}}</td>
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
                                                    label="Apellido y nombre"
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
                                                    :rules="[rules.required, rules.minTelefono, rules.maxTelefono]"
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
                                    @click="contactDialog = true;"
                                >
                                    <v-icon size="medium">fas fa-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Añadir contactos</span>
                        </v-tooltip>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4" class="py-0">
                <v-combobox
                    v-model="$store.state.proveedores.form.provincia"
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
                    v-model="$store.state.proveedores.form.localidad"
                    :items="localidades"
                    :rules="[rules.required]"
                    :disabled="localidades.length == 0"
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
                    v-model="$store.state.proveedores.form.codigopostal"
                    :rules="mode != 'edit' ? [rules.required, rules.postal] : [rules.required]"
                    label="Código postal"
                    required
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0">
                <v-textarea
                    v-model="$store.state.proveedores.form.observaciones"
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
            max: value =>
                (value && value.length <= 190) ||
                "Este campo no puede contener más de 190 digitos",
            cuit: value =>
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
            if (this.$store.state.proveedores.form.provincia) {
                if (this.$store.state.proveedores.form.provincia.id) {
                    let data = Localidades.find(
                        localidad =>
                            localidad.id ===
                            this.$store.state.proveedores.form.provincia.id
                    );
                    if (data.ciudades) {
                        return data.ciudades;
                    } else {
                        return [];
                    }
                } else {
                    return [];
                }
            } else {
                return [];
            }
        }
    },

    created() {
        if (
            this.mode == "edit" &&
            this.$store.state.proveedores.form.provincia
        ) {
            let provincia = Provincias.provincias.find(
                provincia =>
                    provincia.nombre ==
                    this.$store.state.proveedores.form.provincia
            );
            this.$store.state.proveedores.form.provincia = provincia;

            let data = Localidades.find(
                localidad => localidad.id === provincia.id
            );

            let ciudad = data.ciudades.find(
                ciudad =>
                    ciudad.nombre ==
                    this.$store.state.proveedores.form.localidad
            );
            this.$store.state.proveedores.form.localidad = ciudad;

            this.contactos = this.$store.state.proveedores.form.contactos;
        }
    },

    methods: {
        addContact: async function() {
            if (this.$refs.contactForm.validate()) {
                let contacto = this.contactForm;
                this.contactos.push(contacto);
                this.$store.state.proveedores.form.contactos = this.contactos;
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
                this.$store.state.proveedores.form.eliminados = this.contactosEliminados;
            }

            let index = this.contactos.indexOf(contacto);
            this.contactos.splice(index, 1);
            this.$store.state.proveedores.form.contactos = this.contactos;
        },

        getProvincia() {
            if (this.$store.state.proveedores.form.provincia) {
                this.$store.state.proveedores.form.provincia = this.$store.state.proveedores.form.provincia.nombre;
            } else {
                this.$store.state.proveedores.form.provincia = null;
            }
        },

        getLocalidad() {
            if (this.$store.state.proveedores.form.localidad) {
                this.$store.state.proveedores.form.localidad = this.$store.state.proveedores.form.localidad.nombre;
            } else {
                this.$store.state.proveedores.form.localidad = null;
            }
        }
    }
};
</script>

<style>
</style>