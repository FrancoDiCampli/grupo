<template>
    <!-- CLIENTE -->
    <v-col cols="12" class="py-0">
        <v-text-field
            v-model="searchCliente"
            :rules="[rules.required]"
            @keyup="searchClienteAfter()"
            class="search-client-input"
            append-icon="fas fa-search"
            label="Cliente"
            outlined
        ></v-text-field>
        <v-card outlined class="search-client-table mb-5" v-if="searchClienteTable">
            <v-row justify="center" v-if="searchInProcess" class="py-5">
                <v-progress-circular :size="70" :width="7" color="primary" indeterminate></v-progress-circular>
            </v-row>
            <div v-else-if="searchCliente != null &&searchCliente != ''">
                <v-simple-table v-if="clientes.length > 0">
                    <thead>
                        <tr>
                            <th class="text-xs-left">Apellido Nombre</th>
                            <th class="text-xs-left">Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(cliente, index) in clientes"
                            :key="index"
                            class="search-client-select"
                            @click="selectCliente(cliente)"
                        >
                            <td>{{cliente.razonsocial}}</td>
                            <td>{{cliente.documentounico}}</td>
                        </tr>
                    </tbody>
                </v-simple-table>
                <div v-else class="py-5">
                    <h3 class="text-center">
                        Ningun dato coincide con lel
                        criterio de busqueda
                    </h3>
                </div>
            </div>
        </v-card>
    </v-col>
</template>

<script>
export default {
    data: () => ({
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            cantidadMaxima: value =>
                value <= Number(cantidadMaxima) ||
                "La cantidad no puede superar el stock existente"
        },
        // CLIENTES
        searchInProcess: false,
        clientes: [],
        searchCliente: null,
        searchClienteTable: false
    }),

    methods: {
        // CLIENTES
        searchClienteAfter() {
            this.searchInProcess = true;
            this.searchClienteTable = true;
            if (this.searchCliente != null && this.searchCliente != "") {
                if (this.searchCliente == "0") {
                    this.searchCliente = "CONSUMIDOR FINAL";
                    this.$store.state.ventas.form.cliente_id = 1;
                    this.clientes = [];
                    this.searchClienteTable = false;
                    this.searchInProcess = false;
                } else {
                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }
                    this.timer = setTimeout(() => {
                        this.findCliente();
                    }, 1000);
                }
            }
        },

        findCliente: async function() {
            this.$store.state.ventas.form.cliente_id = null;
            axios
                .post("/api/buscando", {
                    buscar: this.searchCliente,
                    nuevoComp: true
                })
                .then(response => {
                    this.clientes = response.data.clientes;
                    this.searchInProcess = false;
                })
                .catch(error => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        selectCliente(cliente) {
            this.searchCliente = cliente.razonsocial;
            this.$store.state.ventas.form.cliente_id = cliente.id;
            this.clientes = [];
            this.searchClienteTable = false;
        }
    }
};
</script>

<style>
</style>