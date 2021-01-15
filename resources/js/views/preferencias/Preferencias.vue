<template>
  <div>
      <v-container>
          {{ lightColors.primary }}
        <v-row justify="center">
            <v-col cols="12" md="5">
                <v-card outlined>
                    <v-card-text class="pa-0">
                        <v-expansion-panels accordion>
                            <v-expansion-panel
                                v-for="(color, i) in theme.light" :key="i">
                                <v-expansion-panel-header style="text-transform: capitalize;" >
                                    <div :style="`background-color: ${color};`" class="color-indicator"></div> {{ i }}
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-row justify="center">
                                        <v-color-picker
                                            v-model="lightColors.primary"
                                            :swatches="swatches"
                                            show-swatches
                                            mode=""
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
                        <br>
                        <v-row justify="center">
                            <v-col cols="10">
                                <v-card shaped outlined>
                                    <v-card-text>
                                        <v-simple-table>
                                            <template v-slot:default>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            Nombre
                                                        </th>
                                                        <th class="text-left">
                                                            Apellido
                                                        </th>
                                                        <th class="text-left"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, i) in 3" :key="i">
                                                        <td>Nombre {{ item }}</td>
                                                        <td>Apellido {{ item }}</td>
                                                        <td><v-btn icon><v-icon size="medium" color="secondary">fas fa-ellipsis-v</v-icon></v-btn></td>
                                                    </tr>
                                                </tbody>
                                            </template>
                                        </v-simple-table>
                                    </v-card-text>
                                </v-card>
                                <br>
                                <v-alert
                                    color="error"
                                    border="left"
                                    colored-border
                                    class="alert-border"
                                    style="border: thin solid #e0e0e0; border-left: none;"
                                >
                                    <div class="ma-2">
                                        <h3>Grupo APC</h3>
                                        <p class="mt-2 mb-4">Grupo apc grupo apc grupo apc</p>
                                    </div>
                                    <v-row justify="end" class="my-2">
                                        <v-btn tile color="error" dark class="mx-2 elevation-0">Cancelar</v-btn>
                                        <v-btn tile color="success" dark class="mx-2 elevation-0">Aceptar</v-btn>
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
            light: {
                primary: "#44C2F7",
                secondary: "#8DC638",
                error: "#F44336",
                success: "#4CAF50",
                warning: "#FFC107"
            }
        },

        swatches: [
            ['#44C2F7', '#0000FF', '#0000AA', '#000055'],
            ['#00FF00', "#8DC638", '#00AA00', '#005500'],
            ['#FFFF00', "#FFC107", '#AAAA00', '#555500'],
            ["#F44336", '#FF0000', '#AA0000', '#550000'],
        ],
    }),

    computed: {
        lightColors: {
            set() {

            },
            get() {
              return {
                  primary: this.hexToRgb("#44C2F7"),
                secondary: "#8DC638",
                error: "#F44336",
                success: "#4CAF50",
                warning: "#FFC107"
              }  
                
            }
        }
    },

    methods: {
        hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    }
}
</script>

<style lang="scss">

.color-indicator {
    max-width: 30px;
    height: 30px;
    border-radius: 15px;
    margin-right: 8px;
}

</style>