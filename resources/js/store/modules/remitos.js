const state = {
    remitos: null,
    venta: null,
    form: {}
};

const mutations = {
    fillRemitos(state, remitos) {
        state.remitos = {
            remitos: remitos.ventas,
            total: remitos.total,
            ultima: remitos.ultimo,
            eliminadas: remitos.eliminadas
        };
    },

    fillVenta(state, venta) {
        state.venta = venta;
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
                .get("/api/remitos", { params: params })
                .then(response => {
                    commit("fillRemitos", response.data);
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
                .get("/api/remitos/" + params.id)
                .then(response => {
                    commit("fillVenta", response.data);
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
                .post("/api/remitos", state.form)
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

    edit({ commit }, params) {
        commit("fillForm", params.data);
    },

    update({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .put("/api/remitos/" + params.id, state.form)
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
                .delete("/api/remitos/" + params.id)
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
