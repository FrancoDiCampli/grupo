const state = {
    entregas: null,
    entrega: null,
    selected: null,
    form: {}
};

const mutations = {
    fillEntregas(state, entregas) {
        state.entregas = entregas;
    },

    fillEntrega(state, entrega) {
        state.entrega = entrega;
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
                .get("/api/entregas", { params: params })
                .then(response => {
                    console.log(response.data);
                    commit("fillEntregas", response.data);
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
                .get("/api/entregas/" + params.id)
                .then(response => {
                    commit("fillEntrega", response.data);
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

    entregar({ commit }, params) {
        return new Promise(resolve => {
            commit("fillForm", params);
            resolve();
        });
    },

    save({ state, commit, dispatch }) {
        console.log(state.form);
        return new Promise((resolve, reject) => {
            axios
                .post("/api/entregas", state.form)
                .then(response => {
                    commit("resetForm");
                    console.log(response.data);
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
