<template>
    <div>
        <div v-if="inProcess">
            <v-row justify="center" style="margin-top: 50px;">
                <v-progress-circular
                    :size="70"
                    :width="7"
                    color="secondary"
                    indeterminate
                ></v-progress-circular>
            </v-row>
        </div>
        <div v-else>
            <div v-if="cuentasActivas.length > 0">
                <v-data-table
                    v-model="selected"
                    :headers="
                        $vuetify.breakpoint.xsOnly ? headersMobile : headers
                    "
                    :items="cuentasActivas"
                    item-key="id"
                    :expanded.sync="selected"
                    show-select
                    hide-default-footer
                    :items-per-page="-1"
                    :mobile-breakpoint="0"
                >
                    <template v-slot:expanded-item="{ headers, item }">
                        <td :colspan="headers.length" class="py-5">
                            <v-card class="elevation-0" outlined>
                                <v-card-title class="py-2">Pagos:</v-card-title>
                                <v-divider></v-divider>
                                <v-speed-dial
                                    v-model="fabs[item.id]"
                                    direction="left"
                                    class="fabPay"
                                >
                                    <template v-slot:activator>
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn
                                                    v-on="on"
                                                    v-model="fabs[item.id]"
                                                    @click="
                                                        fabs[item.id] = !fabs[
                                                            item.id
                                                        ]
                                                    "
                                                    color="secondary"
                                                    dark
                                                    small
                                                    fab
                                                >
                                                    <v-icon v-if="fabs[item.id]"
                                                        >fas fa-times</v-icon
                                                    >
                                                    <v-icon v-else
                                                        >fas fa-plus</v-icon
                                                    >
                                                </v-btn>
                                            </template>
                                            <span v-if="fabs[item.id]"
                                                >Cerrar</span
                                            >
                                            <span v-else>Añadir pago</span>
                                        </v-tooltip>
                                    </template>
                                    <v-btn
                                        fab
                                        dark
                                        small
                                        color="deep-purple"
                                        v-if="
                                            $store.state.clientes.cliente
                                                .haber > 0
                                        "
                                        @click="cuentaOnFocus(item.id, 'haber')"
                                    >
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-icon
                                                    v-on="on"
                                                    style="font-size: 18px;"
                                                    >fas
                                                    fa-hand-holding-usd</v-icon
                                                >
                                            </template>
                                            <span>Haber</span>
                                        </v-tooltip>
                                    </v-btn>
                                    <v-btn
                                        fab
                                        dark
                                        small
                                        color="amber"
                                        @click="
                                            cuentaOnFocus(
                                                item.id,
                                                'transferencia'
                                            )
                                        "
                                    >
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-icon
                                                    v-on="on"
                                                    style="font-size: 18px;"
                                                    >fas fa-exchange-alt</v-icon
                                                >
                                            </template>
                                            <span>Transferencia</span>
                                        </v-tooltip>
                                    </v-btn>
                                    <v-btn
                                        fab
                                        dark
                                        small
                                        color="blue"
                                        @click="
                                            cuentaOnFocus(item.id, 'cheque')
                                        "
                                    >
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-icon
                                                    v-on="on"
                                                    style="font-size: 18px;"
                                                    >fas
                                                    fa-money-check-alt</v-icon
                                                >
                                            </template>
                                            <span>Cheque</span>
                                        </v-tooltip>
                                    </v-btn>
                                    <v-btn
                                        fab
                                        dark
                                        small
                                        color="teal"
                                        @click="
                                            cuentaOnFocus(item.id, 'efectivo')
                                        "
                                    >
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-icon
                                                    v-on="on"
                                                    style="font-size: 18px;"
                                                    >fas fa-money-bill</v-icon
                                                >
                                            </template>
                                            <span>Efectivo</span>
                                        </v-tooltip>
                                    </v-btn>
                                </v-speed-dial>
                                <v-card-text class="ma-0 pa-0">
                                    <v-simple-table v-if="pagos.length > 0">
                                        <thead>
                                            <tr>
                                                <th class="text-xs-left">
                                                    Tipo
                                                </th>
                                                <th class="txt-xs-left">
                                                    Monto
                                                </th>
                                                <th class="text-xs-left">
                                                    <span class="hidden-xs-only"
                                                        >Monto en</span
                                                    >
                                                    pesos
                                                </th>
                                                <th class="text-xs-left"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(pago, index) in pagos[
                                                    item.id
                                                ].pagos"
                                                :key="index"
                                            >
                                                <td>{{ pago.tipo }}</td>
                                                <td>{{ pago.dolares | formatCurrency('USD') }}</td>
                                                <td>{{ pago.pesos | formatCurrency('ARS') }}</td>

                                                <td>
                                                    <v-btn
                                                        color="secondary"
                                                        icon
                                                        @click="
                                                            deletePay(
                                                                item.id,
                                                                pago
                                                            )
                                                        "
                                                    >
                                                        <v-icon size="medium"
                                                            >fas
                                                            fa-times</v-icon
                                                        >
                                                    </v-btn>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-simple-table>
                                </v-card-text>
                            </v-card>
                            <br />
                        </td>
                    </template>
                </v-data-table>
                <v-row justify="center">
                    <v-btn
                        tile
                        color="secondary"
                        :class="selected.length > 0 ? '' : 'mt-5'"
                        outlined
                        @click="pagar()"
                        :disabled="disabledPay"
                        >Pagar</v-btn
                    >
                </v-row>
            </div>
            <div v-else>
                <br />
                <h2 class="text-center">El cliente no posee cuentas activas</h2>
            </div>
        </div>

        <!-- Formulario para pagos en efectivo -->
        <v-dialog
            v-model="dialogs.efectivo"
            width="600"
            persistent
            no-click-animation
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <v-card>
                <v-card-title primary-title>Efectivo</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="adding">
                    <v-row justify="center" class="my-5">
                        <v-progress-circular
                            :size="70"
                            :width="7"
                            color="secondary"
                            indeterminate
                        ></v-progress-circular>
                    </v-row>
                </v-card-text>
                <v-form
                    v-else
                    ref="efectivoForm"
                    @submit.prevent="addPay('Efectivo')"
                >
                    <v-card-text>
                        <br />
                        <v-row justify="space-around">
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="dolares"
                                    :rules="[rules.required]"
                                    @focus="inputFocus = 'dolares'"
                                    label="Monto"
                                    outlined
                                    type="number"
                                ></v-text-field>
                                <v-text-field
                                    v-model="pesos"
                                    :rules="[rules.required]"
                                    @focus="inputFocus = 'pesos'"
                                    label="Monto en pesos"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="divisa.cotizacion"
                                    :rules="[rules.required]"
                                    label="Cotizacion"
                                    outlined
                                    type="number"
                                ></v-text-field>
                                <v-dialog
                                    ref="dialogFecha"
                                    v-model="fechaDialog"
                                    :return-value.sync="divisa.fechaCotizacion"
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                divisa.fechaCotizacion
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (divisa.fechaCotizacion = value)
                                            "
                                            label="Fecha de la cotizacion"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="divisa.fechaCotizacion"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogFecha.save(
                                                    divisa.fechaCotizacion
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="end">
                            <v-btn
                                color="secondary"
                                tile
                                outlined
                                @click="dialogs.efectivo = false"
                                >Cancelar</v-btn
                            >
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mx-3"
                                type="submit"
                                >Añadir</v-btn
                            >
                        </v-row>
                    </v-card-text>
                </v-form>
            </v-card>
        </v-dialog>

        <!-- Formulario para pagos con cheque -->
        <v-dialog
            v-model="dialogs.cheque"
            width="600"
            persistent
            no-click-animation
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <v-card>
                <v-card-title primary-title>Cheque</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="adding">
                    <v-row justify="center" class="my-5">
                        <v-progress-circular
                            :size="70"
                            :width="7"
                            color="secondary"
                            indeterminate
                        ></v-progress-circular>
                    </v-row>
                </v-card-text>
                <v-form
                    v-else
                    ref="chequeForm"
                    @submit.prevent="addPay('Cheque')"
                >
                    <v-card-text>
                        <br />

                        <v-row justify="space-around">
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="dolares"
                                    :rules="[rules.required]"
                                    @focus="inputFocus = 'dolares'"
                                    label="Monto"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="divisa.cotizacion"
                                    :rules="[rules.required]"
                                    label="Cotizacion"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="pesos"
                                    :rules="[rules.required]"
                                    @focus="inputFocus = 'pesos'"
                                    label="Monto en pesos"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-dialog
                                    ref="dialogFecha"
                                    v-model="fechaDialog"
                                    :return-value.sync="divisa.fechaCotizacion"
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                divisa.fechaCotizacion
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (divisa.fechaCotizacion = value)
                                            "
                                            label="Fecha de la cotizacion"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="divisa.fechaCotizacion"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogFecha.save(
                                                    divisa.fechaCotizacion
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="chequesForm.numero"
                                    :rules="[rules.required]"
                                    label="Número"
                                    outlined
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-combobox
                                    v-model="bancosForm.banco"
                                    :items="bancos"
                                    :rules="[rules.required]"
                                    item-text="nombre"
                                    item-value="nombre"
                                    label="Bancos"
                                    required
                                    editable
                                    outlined
                                    return-object
                                ></v-combobox>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-dialog
                                    ref="dialogChequeFechaRecibido"
                                    v-model="chequeFechaRecibidoDialog"
                                    :return-value.sync="
                                        chequesForm.fecharecibido
                                    "
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                chequesForm.fecharecibido
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (chequesForm.fecharecibido = value)
                                            "
                                            label="Fecha recibido"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="chequesForm.fecharecibido"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogChequeFechaRecibido.save(
                                                    chequesForm.fecharecibido
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-dialog
                                    ref="dialogChequeFechaCobro"
                                    v-model="chequeFechaCobroDialog"
                                    :return-value.sync="chequesForm.fechacobro"
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                chequesForm.fechacobro
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (chequesForm.fechacobro = value)
                                            "
                                            label="Diferido"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="chequesForm.fechacobro"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogChequeFechaCobro.save(
                                                    chequesForm.fechacobro
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                            <v-col cols="6" class="py-0">
                                <v-text-field
                                    v-model="chequesForm.cuit"
                                    :rules="[rules.required, rules.du]"
                                    label="CUIT / CUIL"
                                    required
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="chequesForm.emisor"
                                    :rules="[rules.required]"
                                    label="Emisor"
                                    outlined
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" class="py-0">
                                <v-textarea
                                    v-model="observaciones"
                                    outlined
                                    label="Observaciones"
                                    no-resize
                                    rows="3"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="end">
                            <v-btn
                                color="secondary"
                                tile
                                outlined
                                @click="dialogs.cheque = false"
                                >Cancelar</v-btn
                            >
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mx-3"
                                type="submit"
                                >Añadir</v-btn
                            >
                        </v-row>
                    </v-card-text>
                </v-form>
            </v-card>
        </v-dialog>

        <!-- Formulario para pagos con transferencia -->
        <v-dialog
            v-model="dialogs.transferencia"
            width="600"
            persistent
            no-click-animation
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <v-card>
                <v-card-title primary-title>Transferencia</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="adding">
                    <v-row justify="center" class="my-5">
                        <v-progress-circular
                            :size="70"
                            :width="7"
                            color="secondary"
                            indeterminate
                        ></v-progress-circular>
                    </v-row>
                </v-card-text>
                <v-form
                    v-else
                    ref="transferenciaForm"
                    @submit.prevent="addPay('Transferencia')"
                >
                    <v-card-text>
                        <br />
                        <v-row justify="space-around">
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="dolares"
                                    :rules="[rules.required]"
                                    @focus="inputFocus = 'dolares'"
                                    label="Monto"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="divisa.cotizacion"
                                    :rules="[rules.required]"
                                    label="Cotizacion"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="pesos"
                                    :rules="[rules.required]"
                                    @focus="inputFocus = 'pesos'"
                                    label="Monto en pesos"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-dialog
                                    ref="dialogFecha"
                                    v-model="fechaDialog"
                                    :return-value.sync="divisa.fechaCotizacion"
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                divisa.fechaCotizacion
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (divisa.fechaCotizacion = value)
                                            "
                                            label="Fecha de la cotizacion"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="divisa.fechaCotizacion"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogFecha.save(
                                                    divisa.fechaCotizacion
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="transferenciaForm.numero"
                                    :rules="[rules.required]"
                                    label="Número"
                                    outlined
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-combobox
                                    v-model="bancosForm.banco"
                                    :items="bancos"
                                    :rules="[rules.required]"
                                    item-text="nombre"
                                    item-value="nombre"
                                    label="Bancos"
                                    required
                                    editable
                                    outlined
                                    return-object
                                ></v-combobox>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-dialog
                                    ref="dialogTransferenciaFecha"
                                    v-model="transferenciaFechaDialog"
                                    :return-value.sync="transferenciaForm.fecha"
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                transferenciaForm.fecha
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (transferenciaForm.fecha = value)
                                            "
                                            label="Fecha recibido"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="transferenciaForm.fecha"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogTransferenciaFecha.save(
                                                    transferenciaForm.fecha
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>

                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="transferenciaForm.emisor"
                                    :rules="[rules.required]"
                                    label="Emisor"
                                    outlined
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" class="py-0">
                                <v-text-field
                                    v-model="transferenciaForm.cuit"
                                    :rules="[rules.required, rules.du]"
                                    label="CUIT / CUIL"
                                    required
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" class="py-0">
                                <v-textarea
                                    v-model="observaciones"
                                    outlined
                                    label="Observaciones"
                                    no-resize
                                    rows="3"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row justify="end">
                            <v-btn
                                color="secondary"
                                tile
                                outlined
                                @click="dialogs.transferencia = false"
                                >Cancelar</v-btn
                            >
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mx-3"
                                type="submit"
                                >Añadir</v-btn
                            >
                        </v-row>
                    </v-card-text>
                </v-form>
            </v-card>
        </v-dialog>

        <!-- Formulario para pagos con haber -->
        <v-dialog
            v-model="dialogs.haber"
            width="600"
            persistent
            no-click-animation
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <v-card>
                <v-card-title primary-title>Haber</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="adding">
                    <v-row justify="center" class="my-5">
                        <v-progress-circular
                            :size="70"
                            :width="7"
                            color="secondary"
                            indeterminate
                        ></v-progress-circular>
                    </v-row>
                </v-card-text>
                <v-form
                    v-else
                    ref="haberForm"
                    @submit.prevent="addPay('Haber')"
                >
                    <v-card-text>
                        <br />
                        <v-row justify="space-around">
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="dolares"
                                    :rules="[rules.required, rules.haberMaximo]"
                                    @focus="inputFocus = 'dolares'"
                                    label="Monto"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="divisa.cotizacion"
                                    :rules="[rules.required]"
                                    label="Cotizacion"
                                    outlined
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-text-field
                                    v-model="pesos"
                                    :rules="[rules.required]"
                                    label="Monto en pesos"
                                    outlined
                                    disabled
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" class="py-0">
                                <v-dialog
                                    ref="dialogFecha"
                                    v-model="fechaDialog"
                                    :return-value.sync="divisa.fechaCotizacion"
                                    persistent
                                    :width="
                                        $vuetify.breakpoint.xsOnly
                                            ? '100%'
                                            : '300px'
                                    "
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            :value="
                                                divisa.fechaCotizacion
                                                    | formatDate
                                            "
                                            @input="
                                                value =>
                                                    (divisa.fechaCotizacion = value)
                                            "
                                            label="Fecha de la cotizacion"
                                            :rules="[rules.required]"
                                            readonly
                                            outlined
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="divisa.fechaCotizacion"
                                        scrollable
                                        locale="es"
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="
                                                $refs.dialogFecha.save(
                                                    divisa.fechaCotizacion
                                                )
                                            "
                                            >Aceptar</v-btn
                                        >
                                    </v-date-picker>
                                </v-dialog>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-text>
                        <v-row justify="end">
                            <v-btn
                                color="secondary"
                                tile
                                outlined
                                @click="dialogs.haber = false"
                                >Cancelar</v-btn
                            >
                            <v-btn
                                color="secondary"
                                tile
                                class="elevation-0 mx-3"
                                type="submit"
                                >Añadir</v-btn
                            >
                        </v-row>
                    </v-card-text>
                </v-form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
var haberRule = 0;

import moment from "moment";
import Bancos from "../../utils/bancos";

export default {
    data: () => ({
        inProcess: false,
        // Cuentas Seleccionadas
        selected: [],
        // Headers para dispositivos standard
        headers: [
            { text: "Venta N°", value: "factura.numventa", sortable: false },
            { text: "Importe", value: "importe", sortable: false },
            { text: "Saldo", value: "saldo", sortable: false },
            { text: "Alta", value: "alta", sortable: false },
            { text: "Ultimo pago", value: "ultimopago", sortable: false }
        ],
        // Headers para dispositivos mobiles
        headersMobile: [
            { text: "Venta N°", value: "factura.numventa", sortable: false },
            { text: "Saldo", value: "saldo", sortable: false },
            { text: "Alta", value: "alta", sortable: false }
        ],
        // Forularios_________________________________________________________
        // Cotizacion
        fechaDialog: false,
        divisa: {},
        // Variable para indicar el inicio y finalizacion del proceso de añadir un pago
        adding: false,
        // Array con los pagos de cada cuenta
        pagos: [],
        // Cuenta activa al activarse el formulario
        cuentaFocus: null,
        // Variables para los dialogs con los formularios
        dialogs: {
            efectivo: false,
            cheque: false,
            transferencia: false,
            haber: false
        },
        pesos: null,
        dolares: null,
        // Variables para el formulario de cheques
        chequesForm: {
            numero: null,
            fecharecibido: null,
            fechacobro: null,
            banco: null,
            cuit: null,
            emisor: null,
            estado: null
        },
        // Estados de los cheques
        estados: ["POR COBRAR", "COBRADO"],
        // Variables para el formulario de cheques
        transferenciaForm: {
            numero: null,
            fecha: null,
            banco: null,
            cuit: null,
            emisor: null
        },
        observaciones: null,
        // Bancos
        bancos: Bancos.bancos,
        bancosForm: {},
        // Variables para los date pickers
        chequeFechaRecibidoDialog: false,
        chequeFechaCobroDialog: false,
        transferenciaFechaDialog: false,
        // Input activo durante la edición del formulario
        inputFocus: null,
        // Reglas para los formularios
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            haberMaximo: value =>
                value <= Number(haberRule) ||
                "El monto no puede ser mayor al haber de la cuenta",
            du: value =>
                (value && value.length < 12 && value.length > 10) ||
                "Este campo debe contener 11 digitos"
        }
    }),

    computed: {
        // Array con las cuentas activas
        cuentasActivas() {
            let cuentas = this.$store.state.clientes.cliente.cuentas;
            let arrayCuentasActivas = [];
            if (cuentas.length > 0) {
                for (let i = 0; i < cuentas.length; i++) {
                    if (cuentas[i].estado == "ACTIVA") {
                        arrayCuentasActivas.push(cuentas[i]);
                    }
                }
            }
            return arrayCuentasActivas;
        },

        // Array con los fabs
        fabs() {
            let arrayFabs = [];
            for (let i = 0; i < this.cuentasActivas.length; i++) {
                arrayFabs[this.cuentasActivas[i].id] = false;
            }
            return arrayFabs;
        },

        // Habilitar boton pagar
        disabledPay() {
            if (this.pagos.length > 0) {
                for (let i = 0; i < this.pagos.length; i++) {
                    if (this.pagos[i]) {
                        if (this.pagos[i].pagos.length > 0) {
                            return false;
                        }
                    }
                }

                return true;
            } else {
                return true;
            }
        }

        // Cotización
        // latinDate: {
        //     set() {},
        //     get() {
        //         if (this.fechaCotizacion) {
        //             let date = moment(this.fechaCotizacion).format(
        //                 "DD-MM-YYYY"
        //             );
        //             return date;
        //         }
        //     }
        // }
    },

    watch: {
        // Modificador del dolar o pesos cuando cambia la cotización
        cotizacion() {
            this.dolares = Number(this.pesos / this.divisa.cotizacion).toFixed(
                2
            );
            this.pesos = Number(this.dolares * this.divisa.cotizacion).toFixed(
                2
            );
        },

        // Modificador del dolar cuando cambian los pesos
        pesos() {
            if (this.inputFocus == "pesos") {
                if (this.pesos) {
                    this.dolares = Number(
                        this.pesos / this.divisa.cotizacion
                    ).toFixed(2);
                } else {
                    this.dolares = null;
                }
            }
        },

        // Modificador del peso cuando cambian los dolares
        dolares() {
            if (this.inputFocus == "dolares") {
                if (this.dolares) {
                    this.pesos = Number(
                        this.dolares * this.divisa.cotizacion
                    ).toFixed(2);
                } else {
                    this.pesos = null;
                }
            }
        }
    },

    mounted: async function() {
        this.inProcess = true;
        await this.consultarDivisa();
        await this.pagosControl();
        await this.setHaber();
        this.inProcess = false;
    },

    methods: {
        // Consultar el intercabio de divizas de la API
        consultarDivisa() {
            return new Promise(resolve => {
                axios
                    .get("/api/consultar")
                    .then(response => {
                        this.divisa = {
                            cotizacion: response.data.valor,
                            fechaCotizacion: response.data.fecha
                        };
                        resolve(response.data);
                    })
                    .catch(error => {
                        this.inProcess = false;
                        throw new Error(error);
                    });
            });
        },

        pagosControl() {
            let arrayPagos = [];

            if (this.cuentasActivas.length > 0) {
                for (let i = 0; i < this.cuentasActivas.length; i++) {
                    arrayPagos[this.cuentasActivas[i].id] = {
                        cuenta_id: this.cuentasActivas[i].id,
                        pagos: []
                    };
                }
            }

            this.pagos = arrayPagos;
        },

        setHaber() {
            if (this.$store.state.clientes.cliente.haber) {
                haberRule = this.$store.state.clientes.cliente.haber;
            }

            if (this.pagos.length > 0) {
                for (let i = 0; i < this.pagos.length; i++) {
                    if (this.pagos[i]) {
                        if (this.pagos[i].pagos.length > 0) {
                            for (
                                let j = 0;
                                j < this.pagos[i].pagos.length;
                                j++
                            ) {
                                if (this.pagos[i].pagos[j].tipo == "Haber") {
                                    haberRule =
                                        haberRule -
                                        Number(this.pagos[i].pagos[j].dolares);
                                }
                            }
                        }
                    }
                }
            }
        },

        // Establecer sobre que cuenta se agregaran los pagos y activar el formulario correspondiente
        cuentaOnFocus(id, dialog) {
            // Cerrar el fab
            this.fabs[id] = false;

            // Establecer sobre que cuenta se agregaran los pagos
            this.cuentaFocus = id;

            // Activar el dialog correspondiente
            this.dialogs[dialog] = true;
        },

        // Añadir un pago a la cuenta seleccionada
        addPay: async function(type) {
            // Añadir pago en efectivo
            this.adding = true;
            window.localStorage.setItem("divisa", JSON.stringify(this.divisa));
            if (type == "Efectivo") {
                if (this.$refs.efectivoForm.validate()) {
                    let pago = {
                        tipo: type,
                        cotizacion: this.divisa.cotizacion,
                        fecha_cotizacion: this.divisa.fechaCotizacion,
                        pesos: this.pesos,
                        dolares: this.dolares
                    };

                    this.pagos[this.cuentaFocus].pagos.push(pago);

                    this.dialogs.efectivo = false;
                }
            } else if (type == "Cheque") {
                if (this.$refs.chequeForm.validate()) {
                    let pago = {
                        tipo: type,
                        cotizacion: this.divisa.cotizacion,
                        fecha_cotizacion: this.divisa.fechaCotizacion,
                        pesos: this.pesos,
                        dolares: this.dolares,
                        observaciones: this.observaciones
                    };

                    let bank = this.bancosForm.banco.nombre;
                    this.chequesForm.banco = bank;

                    let pagoCheque = Object.assign(pago, this.chequesForm);

                    this.pagos[this.cuentaFocus].pagos.push(pagoCheque);

                    this.$refs.chequeForm.reset();
                    this.dialogs.cheque = false;
                }
            } else if (type == "Transferencia") {
                let pago = {
                    tipo: type,
                    cotizacion: this.divisa.cotizacion,
                    fecha_cotizacion: this.divisa.fechaCotizacion,
                    pesos: this.pesos,
                    dolares: this.dolares,
                    observaciones: this.observaciones
                };

                let bank = this.bancosForm.banco.nombre;
                this.transferenciaForm.banco = bank;

                let pagoTra = Object.assign(pago, this.transferenciaForm);

                this.pagos[this.cuentaFocus].pagos.push(pagoTra);

                this.$refs.transferenciaForm.reset();
                this.dialogs.transferencia = false;
            } else if (type == "Haber") {
                if (this.$refs.haberForm.validate()) {
                    let pago = {
                        tipo: type,
                        cotizacion: this.divisa.cotizacion,
                        fecha_cotizacion: this.divisa.fechaCotizacion,
                        pesos: this.pesos,
                        dolares: this.dolares,
                        observaciones: this.observaciones
                    };

                    this.pagos[this.cuentaFocus].pagos.push(pago);

                    this.$refs.haberForm.reset();
                    this.dialogs.haber = false;
                }
            }
            this.observaciones = null;
            this.divisa = JSON.parse(window.localStorage.getItem("divisa"));
            window.localStorage.removeItem("divisa");
            this.adding = false;
            this.setHaber();
        },

        deletePay(id, pay) {
            let index = this.pagos[id].pagos.indexOf(pay);
            this.pagos[id].pagos.splice(index, 1);
        },

        pagar: async function() {
            this.inProcess = true;

            let formData = [];
            for (let i = 0; i < this.pagos.length; i++) {
                if (this.pagos[i]) {
                    formData.push(this.pagos[i]);
                }
            }

            let response = await this.sendPay(formData);

            await this.$store.dispatch("clientes/show", {
                id: this.$store.state.clientes.cliente.cliente.id
            });
            await this.pagosControl();
            this.selected = [];

            await this.setHaber();
            this.inProcess = false;
        },

        sendPay(formData) {
            this.$store.dispatch("clientes/pay", formData);
        }
    }
};
</script>

<style></style>
