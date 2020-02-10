const state = {
    presupuestos: null,
    presupuesto: null,
    form: {}
};

const mutations = {
    fillPresupuestos(state, presupuestos) {
        state.presupuestos = presupuestos;
    },

    fillPresupuesto(state, presupuesto) {
        state.presupuesto = presupuesto;
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
                .get("/api/presupuestos", { params: params })
                .then(response => {
                    commit("fillPresupuestos", response.data);
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
        return new Promise((resolve, reject) => {
            axios
                .get("/api/presupuestos/" + params.id, { params: params })
                .then(response => {
                    commit("fillPresupuesto", response.data);
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
        return new Promise((resolve, reject) => {
            axios
                .post("/api/presupuestos", state.form)
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

    edit: function({ commit }, params) {
        commit("fillForm", params.data);
    },

    update({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .put("/api/presupuestos/" + params.id, state.form)
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
        return new Promise((resolve, reject) => {
            axios
                .delete("/api/presupuestos/" + params.id)
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
