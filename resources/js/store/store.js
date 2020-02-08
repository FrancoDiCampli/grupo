import Vue from "vue";
import Vuex from "vuex";
import router from "../routes/router";

// Modules
import auth from "./modules/auth";
import sucursales from "./modules/sucursales";
import users from "./modules/users";
import roles from "./modules/roles";

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

        fillErrors(state, error) {
            state.errors.push(error);
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

    actions: {
        errorHandle({ commit, dispatch }, errors) {
            if (errors.status == 401) {
                dispatch("auth/deleteAuthData", {}, { root: true });
            } else if (errors.status == 403) {
                router.push("/accessd_denied");
            } else {
                let error = {
                    data: errors.data,
                    status: errors.status
                };

                commit("fillErrors", error);
            }
        }
    },

    modules: {
        auth: auth,
        sucursales: sucursales,
        users: users,
        roles: roles
    },

    plugins: [processHandle]
});
