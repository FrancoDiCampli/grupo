<template>
    <div>
        <v-container>
            <v-row justify="center">
                <v-col cols="12" md="5">
                    <v-card outlined>
                        <v-card-text class="pa-0">
                            <v-expansion-panels accordion>
                                <v-expansion-panel
                                    v-for="(color, i) in theme.light"
                                    :key="i"
                                >
                                    <v-expansion-panel-header 
                                        style="text-transform: capitalize;"
                                        @click="setColor(color)"
                                    >
                                        <div 
                                            :style="`background-color: ${color.color};`"
                                            class="color-indicator"
                                        ></div>
                                        {{ color.name }}
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row justify="center">
                                            <v-color-picker
                                                v-model="selectedColor.color"
                                                mode="hexa"
                                                hide-mode-switch
                                                show-swatches
                                                :swatches="swatches"
                                            ></v-color-picker>
                                        </v-row>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="5">
                    <v-card outlined>
                        <v-card-text class="pa-0">
                            <v-toolbar color="primary" dark class="elevation-0">
                                <v-toolbar-title>Grupo APC</v-toolbar-title>
                            </v-toolbar>
                            <br />
                            <v-row justify="center">
                                <v-col cols="10">
                                    <v-card shaped outlined>
                                        <v-card-text>
                                            <v-simple-table>
                                                <template v-slot:default>
                                                    <thead>
                                                        <tr>
                                                            <th
                                                                class="text-left"
                                                            >
                                                                Nombre
                                                            </th>
                                                            <th
                                                                class="text-left"
                                                            >
                                                                Apellido
                                                            </th>
                                                            <th
                                                                class="text-left"
                                                            ></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(item,
                                                            i) in 3"
                                                            :key="i"
                                                        >
                                                            <td>
                                                                Nombre
                                                                {{ item }}
                                                            </td>
                                                            <td>
                                                                Apellido
                                                                {{ item }}
                                                            </td>
                                                            <td>
                                                                <v-btn icon
                                                                    ><v-icon
                                                                        size="medium"
                                                                        color="secondary"
                                                                        >fas
                                                                        fa-ellipsis-v</v-icon
                                                                    ></v-btn
                                                                >
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </template>
                                            </v-simple-table>
                                        </v-card-text>
                                    </v-card>
                                    <br />
                                    <v-alert
                                        color="error"
                                        border="left"
                                        colored-border
                                        class="alert-border"
                                        style="border: thin solid #e0e0e0; border-left: none;"
                                    >
                                        <div class="ma-2">
                                            <h3>Grupo APC</h3>
                                            <p class="mt-2 mb-4">
                                                Grupo apc grupo apc grupo apc
                                            </p>
                                        </div>
                                        <v-row justify="end" class="my-2">
                                            <v-btn
                                                tile
                                                color="error"
                                                dark
                                                class="mx-2 elevation-0"
                                                >Cancelar</v-btn
                                            >
                                            <v-btn
                                                tile
                                                color="success"
                                                dark
                                                class="mx-2 elevation-0"
                                                >Aceptar</v-btn
                                            >
                                        </v-row>
                                    </v-alert>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {
    data: () => ({
        theme: {
            dark: false,
            light: [
                {
                    name: 'primary',
                    color: '#44C2F7'
                },
                {
                    name: 'secondary',
                    color: '#8DC638'
                },
                {
                    name: 'error',
                    color: '#F44336'
                },
                {
                    name: 'success',
                    color: '#4CAF50'
                },
                {
                    name: 'warning',
                    color: '#FFC107'
                }
            ]
        },

        swatches: [
            ["#44C2F7", "#0000FF", "#0000AA", "#000055"],
            ["#00FF00", "#8DC638", "#00AA00", "#005500"],
            ["#FFFF00", "#FFC107", "#AAAA00", "#555500"],
            ["#F44336", "#FF0000", "#AA0000", "#550000"]
        ],

        selectedColor: {name: '', color: ''},
    }),

    beforeMount() {
        for (var color in this.$vuetify.theme.themes.light) {
            let selectedIndex = this.theme.light.indexOf(
                this.theme.light.find(e => e.name === color )
            );
            if(this.theme.light[selectedIndex]) {
                this.theme.light[selectedIndex].color = this.$vuetify.theme.themes.light[color];
            }
        }
    },

    watch:{
        'selectedColor.color': function (newVal, oldVal){
            let selectedIndex = this.theme.light.indexOf(
                this.theme.light.find(e => e.name === this.selectedColor.name )
            );
            this.theme.light[selectedIndex].color = this.selectedColor.color;
            this.vuetifyThemeHandler();
        },
    },

    methods: {
        setColor(color) {
            this.selectedColor = color;
        },

        vuetifyThemeHandler() {
            for (var color in this.$vuetify.theme.themes.light) {
                if (this.$vuetify.theme.themes.light.hasOwnProperty(this.selectedColor.name)) {
                    this.$vuetify.theme.themes.light[this.selectedColor.name] = this.selectedColor.color;
                }
            }

            window.localStorage.setItem('customTheme', JSON.stringify(this.$vuetify.theme.themes));
        }
    }
};
</script>

<style lang="scss">
.color-indicator {
    max-width: 30px;
    height: 30px;
    border-radius: 15px;
    margin-right: 8px;
}
</style>
