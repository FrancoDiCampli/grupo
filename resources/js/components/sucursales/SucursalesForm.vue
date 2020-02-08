<template>
    <div>
        <v-row justify="space-around">
            <v-col cols="12" class="py-0">
                <v-text-field
                    v-model="$store.state.sucursales.form.nombre"
                    :rules="[rules.required, rules.max]"
                    label="Nombre"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-combobox
                    v-model="$store.state.sucursales.form.provincia"
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
            <v-col cols="12" sm="6" class="py-0">
                <v-combobox
                    v-model="$store.state.sucursales.form.localidad"
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
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.sucursales.form.direccion"
                    :rules="[rules.required, rules.max]"
                    label="Dirección"
                    required
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="py-0">
                <v-text-field
                    v-model="$store.state.sucursales.form.codigopostal"
                    :rules="mode != 'edit' ? [rules.required, rules.postal] : [rules.required]"
                    label="Código Postal"
                    required
                    outlined
                    type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0">
                <v-textarea
                    v-model="$store.state.sucursales.form.observaciones"
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
            if (this.$store.state.sucursales.form.provincia) {
                if (this.$store.state.sucursales.form.provincia.id) {
                    let data = Localidades.find(
                        localidad =>
                            localidad.id ===
                            this.$store.state.sucursales.form.provincia.id
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
            this.$store.state.sucursales.form.provincia
        ) {
            let provincia = Provincias.provincias.find(
                provincia =>
                    provincia.nombre ==
                    this.$store.state.sucursales.form.provincia
            );
            this.$store.state.sucursales.form.provincia = provincia;

            let data = Localidades.find(
                localidad => localidad.id === provincia.id
            );

            let ciudad = data.ciudades.find(
                ciudad =>
                    ciudad.nombre == this.$store.state.sucursales.form.localidad
            );
            this.$store.state.sucursales.form.localidad = ciudad;
        }
    },

    methods: {
        getProvincia() {
            if (this.$store.state.sucursales.form.provincia) {
                this.$store.state.sucursales.form.provincia = this.$store.state.sucursales.form.provincia.nombre;
            } else {
                this.$store.state.sucursales.form.provincia = null;
            }
        },

        getLocalidad() {
            if (this.$store.state.sucursales.form.localidad) {
                this.$store.state.sucursales.form.localidad = this.$store.state.sucursales.form.localidad.nombre;
            } else {
                this.$store.state.sucursales.form.localidad = null;
            }
        }
    }
};
</script>

<style>
</style>