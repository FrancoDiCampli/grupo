<template>
    <div>
        <v-row justify="space-between">
            <v-col cols="6" sm="4" lg="3">
                <v-list shaped class="mt-10">
                    <v-list-item-group
                        v-model="activeTab"
                        @change="$emit('changetab', activeTab)"
                        color="primary"
                    >
                        <v-list-item
                            v-for="(tab, index) in reports"
                            :key="index"
                        >
                            <v-list-item-content>
                                <v-list-item-title>{{ tab }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-item-group>
                </v-list>
            </v-col>
            <v-col cols="6" sm="8" lg="9">
                <v-fade-transition mode="out-in">
                    <div v-if="activeTab == 0" key="0">
                        <ve-line
                            v-if="$store.state.reportes.ventas"
                            :legend-visible="false"
                            :data="
                                $store.state.reportes.ventas.ventas
                                    .ventasFechaChart
                            "
                        ></ve-line>
                    </div>
                    <div v-if="activeTab == 1" key="1">
                        <ve-pie
                            v-if="$store.state.reportes.ventas"
                            :legend-visible="false"
                            :data="
                                $store.state.reportes.ventas.ventas
                                    .ventasVendedores
                            "
                        ></ve-pie>
                    </div>
                    <div v-if="activeTab == 2" key="2">
                        <ve-pie
                            v-if="$store.state.reportes.ventas"
                            :legend-visible="false"
                            :data="
                                $store.state.reportes.ventas.ventas
                                    .ventasClientes
                            "
                        ></ve-pie>
                    </div>
                    <div v-if="activeTab == 3" key="3">
                        <v-row justify="center" class="mt-12">
                            <v-col cols="10">
                                <v-text-field
                                    v-model="searchCliente"
                                    @keyup="searchClienteAfter()"
                                    class="search-client-input"
                                    append-icon="fas fa-search"
                                    label="Cliente"
                                    outlined
                                ></v-text-field>
                                <v-card
                                    outlined
                                    class="search-client-table mb-5"
                                    v-if="searchClienteTable"
                                >
                                    <v-row
                                        justify="center"
                                        v-if="searchInProcess"
                                        class="py-5"
                                    >
                                        <v-progress-circular
                                            :size="70"
                                            :width="7"
                                            color="primary"
                                            indeterminate
                                        ></v-progress-circular>
                                    </v-row>
                                    <div
                                        v-else-if="
                                            searchCliente != null &&
                                                searchCliente != ''
                                        "
                                    >
                                        <v-simple-table
                                            v-if="clientes.length > 0"
                                        >
                                            <thead>
                                                <tr>
                                                    <th class="text-xs-left">
                                                        Apellido Nombre
                                                    </th>
                                                    <th class="text-xs-left">
                                                        Documento
                                                    </th>
                                                    <th>Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(cliente,
                                                    index) in clientes"
                                                    :key="index"
                                                    class="search-client-select"
                                                    @click="
                                                        selectCliente(cliente)
                                                    "
                                                >
                                                    <td>
                                                        {{
                                                            cliente.razonsocial
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            cliente.documentounico
                                                        }}
                                                    </td>
                                                    <td>
                                                        <div
                                                            v-if="
                                                                cliente.distribuidor
                                                            "
                                                        >
                                                            Distribuidor
                                                        </div>
                                                        <div v-else>
                                                            Cliente
                                                        </div>
                                                    </td>
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
                        </v-row>
                        <div v-if="chartData">
                            <ve-pie
                                :data="chartData"
                                :legend-visible="false"
                            ></ve-pie>
                            <v-row justify="center">
                                <v-btn
                                    color="secondary"
                                    tile
                                    class="elevation-0"
                                    >Imprimir</v-btn
                                >
                                <br />
                            </v-row>
                        </div>
                    </div>
                </v-fade-transition>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data: () => ({
        activeTab: 0,
        reports: ["Ventas", "Vendedor", "Client. | Dist.", "Lit. Cliente"],
        // CLIENTES
        searchInProcess: false,
        searchCliente: null,
        searchClienteTable: false,
        clientes: [],

        chartData: null
    }),

    props: ["desde", "hasta"],

    methods: {
        getActiveTab() {
            return this.activeTab;
        },

        // CLIENTES
        searchClienteAfter() {
            this.searchInProcess = true;
            this.searchClienteTable = true;
            if (this.searchCliente != null && this.searchCliente != "") {
                if (this.searchCliente == "0") {
                    this.searchCliente = "CONSUMIDOR FINAL";
                    this.clientes = [];
                    this.searchData(1);
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
            this.clientes = [];

            axios
                .post("/api/buscando", {
                    buscar: this.searchCliente,
                    nuevoComp: true
                })
                .then(response => {
                    let responseClientes = response.data.clientes;
                    let responseDistribuidores = response.data.distribuidores;
                    for (let i = 0; i < responseClientes.length; i++) {
                        this.clientes.push(responseClientes[i]);
                    }
                    for (let i = 0; i < responseDistribuidores.length; i++) {
                        this.clientes.push(responseDistribuidores[i]);
                    }
                    this.searchInProcess = false;
                })
                .catch(error => {
                    console.log(error);
                    this.searchInProcess = false;
                });
        },

        selectCliente(cliente) {
            this.searchCliente = cliente.razonsocial;
            this.clientes = [];
            this.searchClienteTable = false;
            this.searchData(cliente.id);
        },

        async searchData(id) {
            let response = await this.$store.dispatch(
                "reportes/ventasClientesArticulos",
                {
                    id: id,
                    desde: this.desde,
                    hasta: this.hasta
                }
            );

            this.chartData = response;
        }
    }
};
</script>

<style></style>
