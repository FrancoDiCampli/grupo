const state = {
    facturas: null,
    factura: null,
    selected: null,
    form: {}
};

const mutations = {
    fillFacturas(state, facturas) {
        state.facturas = facturas;
    },

    fillFactura(state, factura) {
        state.factura = factura;
    },

    fillSelected(state, selected) {
        state.selected = selected;
    },

    fillForm(state, form) {
        state.form = form;
    },

    resetForm(state) {
        state.form = {};
    }
};

const actions = {
    index({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/facturas", { params: params })
                .then(response => {
                    commit("fillFacturas", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
                });
        });
    },

    show({ commit, dispatch }, params) {
        return new Promise(resolve => {
            axios
                .get("/api/facturas/" + params.id)
                .then(response => {
                    commit("fillFactura", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
                });
        });
    },

    facturar({ commit }, params) {
        return new Promise(resolve => {
            commit("fillForm", {
                detalles: params.details
            });
            resolve();
        });
    },

    save({ state, commit, dispatch }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/facturas", state.form)
                .then(response => {
                    commit("resetForm");
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
                });
        });
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
