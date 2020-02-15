<template>
    <div>
        <v-container>
            <!-- MOVIENTOS -->
            <v-row justify="center">
                <v-col cols="12">
                    <v-tabs right hide-slider background-color="transparent">
                        <v-btn
                            color="primary"
                            class="filter-btn"
                            icon
                            @click="filterMenu = !filterMenu"
                        >
                            <v-icon size="medium">fas fa-filter</v-icon>
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-tab>Articulos</v-tab>
                        <v-tab>Cuentas</v-tab>
                        <v-tab-item>
                            <v-expand-transition>
                                <div class="filters" v-if="filterMenu">
                                    <slot name="filter"></slot>
                                </div>
                            </v-expand-transition>
                            <v-card shaped outlined :loading="$store.state.inProcess">
                                <v-card-title>Movimientos de articulos</v-card-title>
                                <v-divider></v-divider>
                                <v-card-text v-if="$store.state.movimientos.movimientos">
                                    <v-timeline>
                                        <v-timeline-item
                                            v-for="(move, index) in $store.state
                                                .movimientos.movimientos
                                                .articulos"
                                            :key="index"
                                            fill-dot
                                            right
                                            small
                                        >
                                            <template v-slot:opposite>
                                                {{
                                                move.fecha
                                                }}
                                            </template>
                                            <div>
                                                <h2>{{ move.tipo }}</h2>
                                                <v-divider></v-divider>
                                                <br />
                                                <div class="move-info">
                                                    <p>
                                                        <b>Articulo:</b>
                                                        {{
                                                        move.inventario
                                                        .inventario
                                                        .articulo
                                                        .articulo
                                                        }}
                                                    </p>
                                                    <p>
                                                        <b>Cantidad:</b>
                                                        {{ move.cantidad }}
                                                    </p>
                                                    <p>
                                                        <b>Litros:</b>
                                                        {{
                                                        move.inventario
                                                        .cantidadlitros
                                                        }}
                                                    </p>
                                                    <p>
                                                        <b>Lote:</b>
                                                        {{
                                                        move.inventario
                                                        .inventario.lote
                                                        }}
                                                    </p>
                                                    <p>
                                                        <b>Usuario:</b>
                                                        {{
                                                        move.inventario.user
                                                        .name
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </v-timeline-item>
                                    </v-timeline>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>
                        <v-tab-item>
                            <v-expand-transition>
                                <div class="filters" v-if="filterMenu">
                                    <slot name="filter"></slot>
                                </div>
                            </v-expand-transition>
                            <v-card shaped outlined :loading="$store.state.inProcess">
                                <v-card-title>Movimientos de cuentas</v-card-title>
                                <v-divider></v-divider>
                                <v-card-text v-if="$store.state.movimientos.movimientos">
                                    <v-timeline>
                                        <v-timeline-item
                                            v-for="(move, index) in $store.state
                                                .movimientos.movimientos
                                                .cuentas"
                                            :key="index"
                                            fill-dot
                                            right
                                            small
                                        >
                                            <template v-slot:opposite>
                                                {{
                                                move.fecha
                                                }}
                                            </template>
                                            <div>
                                                <h2>{{ move.tipo }}</h2>
                                                <v-divider></v-divider>
                                                <br />
                                                <div class="move-info">
                                                    <p>
                                                        <b>Importe:</b>
                                                        {{ move.importe }}
                                                    </p>
                                                    <p>
                                                        <b>Cliente:</b>
                                                        {{
                                                        move.cuenta.ctacte
                                                        .factura.cliente
                                                        .razonsocial
                                                        }}
                                                    </p>
                                                    <p>
                                                        <b>Usuario:</b>
                                                        {{ move.user.name }}
                                                    </p>
                                                    <p v-if="move.user.negocio">
                                                        <b>Sucursal:</b>
                                                        {{
                                                        move.user.negocio
                                                        .nombre
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </v-timeline-item>
                                    </v-timeline>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>
                    </v-tabs>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {
    data: () => ({
        filterMenu: false
    })
};
</script>

<style>
</style>
