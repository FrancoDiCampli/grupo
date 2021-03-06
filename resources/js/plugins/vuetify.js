import "@fortawesome/fontawesome-free/css/all.css";
import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import es from "vuetify/es5/locale/es";

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        dark: false,
        themes: {
            light: {
                primary: "#44C2F7",
                secondary: "#8DC638",
                accent: "#44C2F7",
                error: "#F44336",
                info: "#00BCD4",
                success: "#4CAF50",
                warning: "#FFC107",
                disabled: "#787878"
            }
        }
    },
    options: {
        customProperties: true
    },
    icons: {
        iconfont: "fa"
    },
    lang: {
        locales: { es },
        current: "es"
    }
});
