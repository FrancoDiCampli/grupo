<template>
    <div>
        <v-row justify="space-between">
            <v-col cols="6" sm="4" lg="3">
                <v-list shaped class="mt-10">
                    <v-list-item-group v-model="activeTab" color="primary">
                        <v-list-item v-for="(tab, index) in reports" :key="index">
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
                            v-if="$store.state.reportes.compras"
                            :legend-visible="false"
                            :data="$store.state.reportes.compras.compras.comprasFechasChart"
                        ></ve-line>
                    </div>
                    <div v-if="activeTab == 1" key="1">
                        <ve-pie
                            v-if="$store.state.reportes.compras"
                            :legend-visible="false"
                            :data="$store.state.reportes.compras.compras.comprasProveedores"
                        ></ve-pie>
                    </div>
                </v-fade-transition>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    data: () => ({
        activeTab: 0,
        reports: ["Compras", "Proveedores"]
    })
};
</script>

<style>
</style>