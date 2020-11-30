<template>
    <div>
        <div
            v-if="
                $store.state.articulos.articulo.articulo.inventarios.length > 0
            "
        >
            <v-data-table
                hide-default-footer
                :headers="headers"
                :items="$store.state.articulos.articulo.inventarios"
            >
                <template v-slot:item="item">
                    <tr>
                        <td>{{ item.item.cantidad | formatNumber }}</td>
                        <td>{{ item.item.cantidadlitros | formatNumber }} Litros</td>
                        <td>
                            <div
                                v-if="item.item.dependencia.id == 1"
                            >{{ item.item.dependencia.razonsocial }}</div>
                            <div
                                v-else
                            >{{ item.item.dependencia.role.role }} - {{ item.item.dependencia.name }}</div>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </div>
        <div v-else>
            <br />
            <h2 class="text-center">¡Este producto no tiene inventario!</h2>
            <br />
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "Cantidad", sortable: false },
            { text: "Cantidad en litros", sortable: false },
            { text: "Proveedor | Consignación", sortable: false }
        ]
    })
};
</script>

<style></style>
