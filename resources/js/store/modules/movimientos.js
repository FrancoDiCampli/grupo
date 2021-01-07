const state = {
    movimientos: null
};

const mutations = {
    fillMovimientos(state, movimientos) {
        state.movimientos = movimientos;
    }
};

const actions = {
    index({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/movimientos", { params: params })
                .then(response => {
                    commit("fillMovimientos", response.data);
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
