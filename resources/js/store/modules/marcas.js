const state = {
    marcas: null
};

const mutations = {
    fillMarcas(state, marcas) {
        state.marcas = marcas;
    }
};

const actions = {
    index({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/marcas", { params: params })
                .then(response => {
                    commit("fillMarcas", response.data);
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
