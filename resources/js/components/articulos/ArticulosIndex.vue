<template>
    <div>
        <v-tabs right hide-slider background-color="transparent">
            <v-tab>
                <v-icon size="medium">fas fa-th-list</v-icon>
            </v-tab>
            <v-tab>
                <v-icon size="medium">fas fa-th-large</v-icon>
            </v-tab>
            <v-tab-item>
                <v-card shaped outlined :loading="$store.state.inProcess">
                    <v-card-title>Articulos</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="px-0" v-if="$store.state.articulos.articulos">
                        <v-data-table
                            hide-default-footer
                            :headers="headers"
                            :items="$store.state.articulos.articulos.articulos"
                            :mobile-breakpoint="0"
                            :items-per-page="-1"
                        >
                            <v-progress-linear v-slot:progress color="primary" indeterminate></v-progress-linear>
                            <template v-slot:item="item">
                                <tr>
                                    <td class="hidden-sm-and-down">{{ item.item.codarticulo }}</td>
                                    <td>{{ item.item.articulo }}</td>
                                    <td>$ {{ item.item.precio }}</td>
                                    <td class="hidden-sm-and-down">
                                        <div v-if="item.item.stock <= 0">0</div>
                                        <div v-else>{{ item.item.stock }}</div>
                                    </td>
                                    <td>
                                        <v-btn
                                            @click="
                                                $router.push(
                                                    '/articulos/show/' +
                                                        item.item.id
                                                )
                                            "
                                            text
                                            icon
                                            color="secondary"
                                        >
                                            <v-icon size="medium">fas fa-ellipsis-v</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                        <slot></slot>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <div v-if="$store.state.inProcess">
                    <v-row justify="center">
                        <v-col cols="10" sm="6" md="4" v-for="i in 3" :key="i">
                            <EskeletonLoader typeString="card, list-item-two-line"></EskeletonLoader>
                        </v-col>
                    </v-row>
                </div>
                <div v-else-if="$store.state.articulos.articulos">
                    <v-row
                        justify="center"
                        v-if="
                            $store.state.articulos.articulos.articulos.length >
                                0
                        "
                    >
                        <v-col
                            cols="10"
                            sm="6"
                            md="4"
                            v-for="articulo in $store.state.articulos.articulos
                                .articulos"
                            :key="articulo.id"
                        >
                            <v-card max-width="300px" shaped outlined>
                                <v-img
                                    class="white--text align-end"
                                    height="250px"
                                    max-width="300px"
                                    :src="articulo.foto"
                                >
                                    <div class="articulos-img-info">
                                        <div
                                            class="right"
                                            @click="
                                                $router.push(
                                                    '/articulos/show/' +
                                                        articulo.id +
                                                        ''
                                                )
                                            "
                                        >
                                            <v-icon>fas fa-ellipsis-v</v-icon>
                                        </div>
                                        <div
                                            v-if="
                                                articulo.stock <=
                                                    articulo.stockminimo &&
                                                    articulo.stock > 0
                                            "
                                        >
                                            <v-tooltip right>
                                                <template v-slot:activator="{ on }">
                                                    <div class="left warning-info" v-on="on">
                                                        <v-icon>
                                                            fas
                                                            fa-box-open
                                                        </v-icon>
                                                    </div>
                                                </template>
                                                <span>Necesita Reposición</span>
                                            </v-tooltip>
                                        </div>
                                        <div
                                            v-else-if="
                                                articulo.stock == 0 ||
                                                    articulo.inventarios
                                                        .length <= 0
                                            "
                                        >
                                            <v-tooltip right>
                                                <template v-slot:activator="{ on }">
                                                    <div class="left error-info" v-on="on">
                                                        <v-icon>
                                                            fas
                                                            fa-exclamation-circle
                                                        </v-icon>
                                                    </div>
                                                </template>
                                                <span>Sin Stock</span>
                                            </v-tooltip>
                                        </div>
                                        <div
                                            v-else-if="
                                                articulo.stock >
                                                    articulo.stockminimo
                                            "
                                        >
                                            <v-tooltip right>
                                                <template v-slot:activator="{ on }">
                                                    <div class="left success-info" v-on="on">
                                                        <v-icon>
                                                            fas
                                                            fa-check
                                                        </v-icon>
                                                    </div>
                                                </template>
                                                <span>Stock Suficiente</span>
                                            </v-tooltip>
                                        </div>
                                    </div>
                                </v-img>

                                <v-card-text>
                                    <h3 class="headline">{{ articulo.articulo }}</h3>
                                    <div>
                                        <p>{{ articulo.litros }} Litros</p>
                                        <p>Total Litros: {{ articulo.stock }}</p>
                                        <p>$ {{ articulo.precio }}</p>
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                    <v-row justify="center" v-else>
                        <h4>No hay datos disponibles</h4>
                    </v-row>
                    <slot></slot>
                </div>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
import EskeletonLoader from "../loaders/EskeletonLoader";

export default {
    data: () => ({
        headers: [
            { text: "Codigo", sortable: false, class: "hidden-sm-and-down" },
            { text: "Articulo", sortable: false },
            { text: "Precio", sortable: false },
            {
                text: "Total Litros",
                sortable: false,
                class: "hidden-sm-and-down"
            },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"],

    components: {
        EskeletonLoader
    }
};
</script>

<style lang="scss"></style>
