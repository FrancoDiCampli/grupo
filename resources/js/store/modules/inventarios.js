const state = {
    inventarios: null,
    form: {}
};

const mutations = {
    fillInventarios(state, inventarios) {
        state.inventarios = inventarios;
    },

    resetForm(state) {
        state.form = {};
    }
};

const actions = {
    index({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/inventarios", { params: params })
                .then(response => {
                    commit("fillInventarios", response.data);
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
                .post("/api/inventarios", state.form)
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
