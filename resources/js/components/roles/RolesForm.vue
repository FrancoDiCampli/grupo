<template>
    <div>
        <v-row justify-center wrap>
            <v-col cols="12" class="py-0">
                <v-text-field
                    v-model="$store.state.roles.form.role"
                    :rules="[rules.required]"
                    label="Rol"
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0" v-if="permisos.length > 0">
                <v-select
                    v-model="$store.state.roles.form.scope"
                    :items="permisos"
                    item-text="description"
                    item-value="permission"
                    :rules="[rules.required]"
                    label="Permisos"
                    multiple
                    outlined
                    chips
                ></v-select>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "RolesForm",

    data() {
        return {
            permisos: [],
            rules: {
                required: value => !!value || "Este campo es obligatorio"
            }
        };
    },

    mounted() {
        this.getPermissions();
    },

    methods: {
        getPermissions: async function() {
            this.permisos = await this.$store.dispatch("roles/permissions");
        }
    }
};
</script>