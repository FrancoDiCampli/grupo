const state = {
    devoluciones: null,
    devolucion: null,
    form: {}
};

const mutations = {
    fillDevoluciones(state, devoluciones) {
        state.devoluciones = devoluciones;
    },

    fillDevolucion(state, devolucion) {
        state.devolucion = devolucion;
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
                .get("/api/devoluciones", { params: params })
                .then(response => {
                    commit("fillDevoluciones", response.data);
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
                .post("/api/devoluciones", state.form)
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

    show({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/devoluciones/" + params.id)
                .then(response => {
                    commit("fillDevolucion", response.data);
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
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
