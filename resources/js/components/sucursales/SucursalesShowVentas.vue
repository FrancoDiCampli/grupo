<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" v-if="$store.state.sucursales.sucursal.ventas.length > 0">
                    <v-simple-table>
                        <thead>
                            <tr>
                                <th class="text-xs-left">Venta N°</th>
                                <th class="text-xs-left">Fecha</th>
                                <th class="text-xs-left">Importe</th>
                                <th class="text-xs-left"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(remito,index) in $store.state.sucursales.sucursal.ventas"
                                :key="index"
                            >
                                <td>{{ remito.numventa }}</td>
                                <td>{{ remito.fecha }}</td>
                                <td>{{ remito.total }}</td>
                                <td>
                                    <v-menu offset-y>
                                        <template v-slot:activator="{ on }">
                                            <v-btn color="secondary" text icon v-on="on">
                                                <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                            </v-btn>
                                        </template>
                                        <v-list>
                                            <v-list-item :to="`/ventas/show/${remito.id}`">
                                                <v-list-item-title>Detalles</v-list-item-title>
                                            </v-list-item>
                                            <v-list-item @click="print(remito.id)">
                                                <v-list-item-title>Imprimir</v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </td>
                            </tr>
                        </tbody>
                    </v-simple-table>
                </v-col>
                <v-col cols="12" v-else>
                    <h2 class="text-center">No se han registrado ventas a nombre de esta sucursal</h2>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {
    methods: {
        print(id) {
            axios({
                url: "/api/ventasPDF/" + id,
                method: "GET",
                responseType: "blob"
            }).then(response => {
                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "venta" + id + ".pdf");
                document.body.appendChild(link);
                link.click();
            });
        }
    }
};
</script>

<style>
</style>