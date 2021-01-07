const state = {
    proveedores: null,
    proveedor: null,
    form: {}
};

const mutations = {
    fillProveedores(state, proveedores) {
        state.proveedores = proveedores;
    },

    fillProveedor(state, proveedor) {
        state.proveedor = proveedor;
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
                .get("/api/suppliers", { params: params })
                .then(response => {
                    commit("fillProveedores", response.data);
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
                .get("/api/suppliers/" + params.id, { params: params })
                .then(response => {
                    commit("fillProveedor", response.data);
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
                .post("/api/suppliers", state.form)
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

    edit: function({ state, commit }, params) {
        params.data.contactos = state.proveedor.contactos;
        commit("fillForm", params.data);
    },

    update({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .put("/api/suppliers/" + params.id, state.form)
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

    destroy({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .delete("/api/suppliers/" + params.id)
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
