const state = {
    clientes: null,
    cliente: null,
    form: {}
};

const mutations = {
    fillClientes(state, clientes) {
        state.clientes = clientes;
    },

    fillCliente(state, cliente) {
        state.cliente = cliente;
    },

    fillForm(state, form) {
        state.form = form;
    },

    resetForm(state) {
        state.form = {};
    }
};

const actions = {
    index({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/clientes", { params: params })
                .then(response => {
                    commit("fillClientes", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    show({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/clientes/" + params.id, { params: params })
                .then(response => {
                    commit("fillCliente", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    miCuenta({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/mi_cuenta", { params: params })
                .then(response => {
                    commit("fillCliente", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    pay({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/pagarcuentas", { pagos: params })
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    save({ state, commit, dispatch }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/clientes", state.form)
                .then(response => {
                    commit("resetForm");
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    edit({ state, commit }, params) {
        params.data.contactos = state.cliente.contactos;
        commit("fillForm", params.data);
    },

    update({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .put("/api/clientes/" + params.id, state.form)
                .then(response => {
                    commit("resetForm");
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    destroy({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .delete("/api/clientes/" + params.id)
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
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
