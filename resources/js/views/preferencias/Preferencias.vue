<template>
    <div>
        <v-container>
           
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <p class="title">Preferencias</p>
                    <p class="subtitle">Colores</p>
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
                                        <div class="my-6">
                                            {{ color.description }}
                                        </div>
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
                    color: '#44C2F7',
                    description: 'El color “primario” (Primary), es usado en los elementos no interactuables del sistema y para indicar ciertos elementos activos, por ejemplo, los elementos del menu lateral.'
                },
                {
                    name: 'secondary',
                    color: '#8DC638',
                    description: 'El color “secundario” (Secondary), es usado para remarcar los elementos interactuables del sistemas, tales como botones, menus, submenus, etc.'
                },
                {
                    name: 'error',
                    color: '#F44336',
                    description: 'El color “error”, no solo es usado en elementos que indican errores en el sistema, sino también en aquellos que advierten al usuario sobre la implicaciones de sus acciones.'
                },
                {
                    name: 'success',
                    color: '#4CAF50',
                    description: 'El color “éxito” (Success), es usado en elementos que indican que una operación fue realizada de forma exitosa o que hay una acción habilitada para dicho elemento.'
                },
                {
                    name: 'warning',
                    color: '#FFC107',
                    description: 'El color “advertencia” (warning), es usado, en menor medida, junto con el color “error” para advertir al usuario sobre sus futuras acciones y para indicar irregularidades en ciertos elementos.'
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
