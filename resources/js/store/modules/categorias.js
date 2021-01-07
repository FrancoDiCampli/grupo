const state = {
    categorias: null
};

const mutations = {
    fillCategorias(state, categorias) {
        state.categorias = categorias;
    }
};

const actions = {
    index({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/categorias", { params: params })
                .then(response => {
                    commit("fillCategorias", response.data);
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
