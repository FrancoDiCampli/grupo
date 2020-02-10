const state = {
    compras: null,
    compra: null,
    form: {}
};

const mutations = {
    fillCompras(state, compras) {
        state.compras = compras;
    },

    fillCompra(state, compra) {
        state.compra = compra;
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
        return new Promise(resolve => {
            axios
                .get("/api/compras", { params: params })
                .then(response => {
                    commit("fillCompras", response.data);
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
                .get("/api/compras/" + params.id, { params: params })
                .then(response => {
                    commit("fillCompra", response.data);
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

    save({ state, commit, dispatch }) {
        return new Promise(resolve => {
            axios
                .post("/api/compras", state.form)
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
    },

    edit({ commit }, params) {
        commit("fillForm", params.data);
    },

    update({ state, commit, dispatch }, params) {
        return new Promise(resolve => {
            axios
                .put("/api/compras/" + params.id, state.form)
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
    },

    destroy({ dispatch }, params) {
        return new Promise(resolve => {
            axios
                .delete("/api/compras/" + params.id)
                .then(response => {
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
