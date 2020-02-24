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
                :items="$store.state.articulos.articulo.articulo.inventarios"
            >
                <template v-slot:item="item">
                    <tr>
                        <td>{{ item.item.cantidad }}</td>
                        <td>{{ item.item.cantidadlitros }}</td>
                        <td v-if="item.item.proveedor">{{ item.item.proveedor.razonsocial }}</td>
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
            { text: "Proveedor", sortable: false }
        ]
    })
};
</script>

<style></style>
