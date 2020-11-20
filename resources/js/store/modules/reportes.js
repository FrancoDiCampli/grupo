const state = {
    cheques: null,
    ventas: null,
    compras: null,
    productosVentas: null,
    productosCompras: null
};

const mutations = {
    fillCheques(state, cheques) {
        state.cheques = cheques;
    },

    fillVentas(state, ventas) {
        state.ventas = ventas;
    },

    fillCompras(state, compras) {
        state.compras = compras;
    },

    fillProductosVentas(state, productosVentas) {
        state.productosVentas = productosVentas;
    },

    fillProductosCompras(state, productosCompras) {
        state.productosCompras = productosCompras;
    }
};

const actions = {
    cartera({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/cartera", { params: params })
                .then(response => {
                    commit("fillCheques", response.data);
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

    cobrar({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/chequeCobrado/" + params.id)
                .then(response => {
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
    },

    ventas({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/estadisticas/ventas", { params: params })
                .then(response => {
                    commit("fillVentas", response.data);
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

    compras({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/estadisticas/compras", { params: params })
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

    detallesVentas({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/estadisticas/detallesVentas", { params: params })
                .then(response => {
                    commit("fillProductosVentas", response.data);
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

    detallesCompras({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/estadisticas/detallesCompras", { params: params })
                .then(response => {
                    commit("fillProductosCompras", response.data);
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
