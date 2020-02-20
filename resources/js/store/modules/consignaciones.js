const state = {
    consignaciones: null,
    consignacion: null,
    form: {}
};

const mutations = {
    fillConsignaciones(state, consignaciones) {
        state.consignaciones = consignaciones;
    },

    fillConsignacion(state, consignacion) {
        state.consignacion = consignacion;
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
                .get("/api/consignaciones", { params: params })
                .then(response => {
                    commit("fillConsignaciones", response.data);
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
                .post("/api/consignaciones", state.form)
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
                .get("/api/consignaciones/" + params.id)
                .then(response => {
                    commit("fillConsignacion", response.data);
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
