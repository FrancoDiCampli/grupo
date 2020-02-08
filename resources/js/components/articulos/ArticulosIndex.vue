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
                            :items-per-page="limit"
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
                                            @click="$router.push('/articulos/show/' + item.item.id);"
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
                <div v-if="$store.state.articulos.articulos">
                    <div v-if="$store.state.inProcess">
                        <v-row justify="center">
                            <v-progress-circular
                                :size="70"
                                :width="7"
                                color="primary"
                                indeterminate
                            ></v-progress-circular>
                        </v-row>
                    </div>
                    <div v-else>
                        <v-row justify="center">
                            <v-col
                                cols="10"
                                sm="6"
                                md="4"
                                v-for="articulo in $store.state.articulos.articulos.articulos"
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
                                                @click="$router.push('/articulos/show/'+articulo.id+'')"
                                            >
                                                <v-icon>fas fa-ellipsis-v</v-icon>
                                            </div>
                                            <div
                                                v-if="articulo.stock <= articulo.stockminimo && articulo.stock > 0"
                                            >
                                                <v-tooltip right>
                                                    <template v-slot:activator="{ on }">
                                                        <div class="left warning-info" v-on="on">
                                                            <v-icon>fas fa-box-open</v-icon>
                                                        </div>
                                                    </template>
                                                    <span>Necesita Reposición</span>
                                                </v-tooltip>
                                            </div>
                                            <div
                                                v-else-if="articulo.stock == 0 || articulo.inventarios.length <= 0"
                                            >
                                                <v-tooltip right>
                                                    <template v-slot:activator="{ on }">
                                                        <div class="left error-info" v-on="on">
                                                            <v-icon>fas fa-exclamation-circle</v-icon>
                                                        </div>
                                                    </template>
                                                    <span>Sin Stock</span>
                                                </v-tooltip>
                                            </div>
                                            <div v-else-if="articulo.stock > articulo.stockminimo">
                                                <v-tooltip right>
                                                    <template v-slot:activator="{ on }">
                                                        <div class="left success-info" v-on="on">
                                                            <v-icon>fas fa-check</v-icon>
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
                                            <p>Stock: {{articulo.stock}}</p>
                                            <p>$ {{ articulo.precio }}</p>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                        <slot></slot>
                    </div>
                </div>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
export default {
    data: () => ({
        headers: [
            { text: "Codigo", sortable: false, class: "hidden-sm-and-down" },
            { text: "Articulo", sortable: false },
            { text: "Precio", sortable: false },
            { text: "Stock", sortable: false, class: "hidden-sm-and-down" },
            { text: "", sortable: false }
        ]
    }),

    props: ["limit"]
};
</script>

<style lang="scss">
.articulos-img-info {
    height: 250px;
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    justify-content: space-between;
    .right {
        width: 0;
        height: 0;
        border-right: 30px solid #8dc638;
        border-top: 30px solid #8dc638;
        border-left: 30px solid transparent;
        border-bottom: 30px solid transparent;
        align-self: flex-end;
        cursor: pointer;
        .v-icon {
            margin: -48px 0px 0px 8px;
            color: white !important;
            font-size: 18px;
        }
    }

    .left {
        width: 0;
        height: 0;
        border-right: 30px solid;
        border-top: 30px solid;
        border-left: 30px solid;
        border-bottom: 30px solid;
        align-self: flex-start;
        .v-icon {
            margin: 0px 0px 0px -20px;
            color: white !important;
            font-size: 18px;
        }
    }

    .success-info {
        border-right-color: transparent !important;
        border-top-color: transparent !important;
        border-left-color: #4caf50 !important;
        border-bottom-color: #4caf50 !important;
    }
    .warning-info {
        border-right-color: transparent !important;
        border-top-color: transparent !important;
        border-left-color: #ffc107 !important;
        border-bottom-color: #ffc107 !important;
    }
    .error-info {
        border-right-color: transparent !important;
        border-top-color: transparent !important;
        border-left-color: #f44336 !important;
        border-bottom-color: #f44336 !important;
    }
}

.v-window-item.v-window-item--active {
    background: #ffffff;
}
</style>