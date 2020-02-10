const state = {
    facturas: null,
    factura: null,
    selected: null,
    form: {}
};

const mutations = {
    fillFacturas(state, facturas) {
        state.facturas = facturas;
    },

    fillFactura(state, factura) {
        state.factura = factura;
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
                .get("/api/facturas", { params: params })
                .then(response => {
                    commit("fillFacturas", response.data);
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
                .get("/api/facturas/" + params.id)
                .then(response => {
                    commit("fillFactura", response.data);
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
                .post("/api/facturas", state.form)
                .then((response, reject) => {
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

    facturar({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            let selected = { seleccionadas: params.selected };
            let detalles = [];
            axios
                .post("/api/facturar", { id: selected })
                .then(response => {
                    for (let i = 0; i < response.data.detalles.length; i++) {
                        let detalle = {
                            articulo_id:
                                response.data.detalles[i].pivot.articulo_id,
                            articulo: response.data.detalles[i].pivot.articulo,
                            cantidad: response.data.detalles[i].pivot.cantidad,
                            litros:
                                response.data.detalles[i].pivot.litros *
                                response.data.detalles[i].pivot.cantidad,
                            precio:
                                response.data.detalles[i].pivot.preciounitario,
                            subtotalDolares:
                                response.data.detalles[i].pivot.subtotal,
                            subtotalPesos:
                                response.data.detalles[i].pivot.subtotalPesos
                        };
                        detalles.push(detalle);
                    }
                    commit("fillForm", {
                        detalles: detalles,
                        ventas: response.data.ventas
                    });
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
