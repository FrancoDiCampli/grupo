<template>
    <v-row justify="center">
        <v-col cols="12" class="py-0 px-5">
            <v-text-field
                v-model="$store.state.auth.form.email"
                :rules="[rules.required]"
                label="Email"
                outlined
            ></v-text-field>
        </v-col>
        <v-col cols="12" class="py-0 px-5">
            <v-text-field
                v-model="$store.state.auth.form.password"
                :rules="[rules.required]"
                :append-icon="password_type ? 'fas fa-eye' : 'fas fa-eye-slash'"
                @click:append="password_type = !password_type"
                :type="password_type ? 'text' : 'password'"
                label="Contraseña"
                outlined
            ></v-text-field>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: "LoginForm",

    data() {
        return {
            password_type: false,
            rules: {
                required: value => !!value || "Este campo es obligatorio"
            }
        };
    }
};
</script>