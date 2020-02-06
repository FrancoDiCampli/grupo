<template>
    <div>
        <v-row justify="center">
            <v-col cols="12" class="py-0 px-5">
                <v-text-field
                    v-model="$store.state.auth.form.name"
                    :rules="[rules.required, rules.max]"
                    label="Nombre"
                    color="primary"
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0 px-5">
                <v-text-field
                    v-model="$store.state.auth.form.email"
                    :rules="[rules.required, rules.max]"
                    label="Email"
                    color="primary"
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0 px-5">
                <v-text-field
                    v-model="$store.state.auth.form.password"
                    :rules="[rules.required, rules.max, rules.min]"
                    :append-icon="
                        password_type ? 'fas fa-eye' : 'fas fa-eye-slash'
                    "
                    @click:append="password_type = !password_type"
                    :type="password_type ? 'text' : 'password'"
                    label="Contraseña"
                    color="primary"
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0 px-5">
                <v-text-field
                    v-model="$store.state.auth.form.password_confirmation"
                    :rules="[
                        rules.required,
                        rules.max,
                        rules.min,
                        rules.password_confirm
                    ]"
                    :append-icon="
                        password_type ? 'fas fa-eye' : 'fas fa-eye-slash'
                    "
                    @click:append="password_type = !password_type"
                    :type="password_type ? 'text' : 'password'"
                    label="Confirmar contraseña"
                    color="primary"
                    outlined
                ></v-text-field>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "RegisterForm",

    data: function() {
        return {
            password_type: false,
            rules: {
                required: value => !!value || "Este campo es obligatorio",
                max: value =>
                    (value && value.length <= 255) ||
                    "Este campo no puede contener mas de 255 digitos",
                min: value =>
                    (value && value.length >= 6) ||
                    "Este campo debe contener al menos 6 digitos",
                password_confirm: value =>
                    (value && value === this.originalPassword) ||
                    "Las contraseñas no coinciden"
            }
        };
    },

    computed: {
        originalPassword() {
            return this.$store.state.auth.form.password
                ? this.$store.state.auth.form.password
                : null;
        }
    }
};
</script>
