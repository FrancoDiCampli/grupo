import Vue from "vue";
import Vuex from "vuex";

// Modules
import auth from "./modules/auth";

// Plugins
import { processHandle } from "./plugins/processHandle";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        inProcess: false,
        errors: []
    },

    mutations: {
        iterateProcess(state, value) {
            state.inProcess = value;
        },

        fillErrors(state, errors) {
            state.errors.push({
                data: errors.data,
                status: errors.status
            });
            state.inProcess = false;
            window.scrollTo(0, 0);
        },

        deleteError(state, error) {
            let index = state.errors.indexOf(error);
            state.errors.splice(index, 1);
        },

        resetErrors(state) {
            state.errors = [];
        }
    },

    modules: {
        auth: auth
    },

    plugins: [processHandle]
});
