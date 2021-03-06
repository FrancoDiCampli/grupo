const state = {
    cheques: null,
    ventas: null,
    compras: null,
    productosVentas: null,
    productosCompras: null,
    ventasClientesArticulos: null
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
    },
    
    fillVentasClientesArticulos(state, ventasClientesArticulos) {
        state.ventasClientesArticulos = ventasClientesArticulos;
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
                    reject(error.response);
                });
        });
    },

    cobrar({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/chequeCobrado/" + params.id)
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
                    reject(error.response);
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
                    reject(error.response);
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
                    reject(error.response);
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
                    reject(error.response);
                });
        });
    },

    ventasClientesArticulos({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/estadisticas/ventasClientesArticulos", { params: params })
                .then(response => {
                    commit("fillVentasClientesArticulos", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                })
        })
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
