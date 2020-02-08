import Vue from "vue";
import Vuex from "vuex";
import router from "../routes/router";

// Modules
import articulos from "./modules/articulos";
import auth from "./modules/auth";
import categorias from "./modules/categorias";
import clientes from "./modules/clientes";
import compras from "./modules/compras";
import facturas from "./modules/facturas";
import inventarios from "./modules/inventarios";
import marcas from "./modules/marcas";
import movimientos from "./modules/movimientos";
import presupuestos from "./modules/presupuestos";
import proveedores from "./modules/proveedores";
import reportes from "./modules/reportes";
import roles from "./modules/roles";
import sucursales from "./modules/sucursales";
import users from "./modules/users";
import ventas from "./modules/ventas";

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
        articulos: articulos,
        auth: auth,
        categorias: categorias,
        clientes: clientes,
        compras: compras,
        facturas: facturas,
        inventarios: inventarios,
        marcas: marcas,
        movimientos: movimientos,
        presupuestos: presupuestos,
        proveedores: proveedores,
        reportes: reportes,
        roles: roles,
        sucursales: sucursales,
        users: users,
        ventas: ventas
    },

    plugins: [processHandle]
});
