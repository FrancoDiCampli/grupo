const state = {
    pedidos: null,
    pedido: null,
    form: {}
};

const mutations = {
    fillPedidos(state, pedidos) {
        state.pedidos = {
            pedidos: pedidos.presupuestos,
            total: pedidos.total,
            ultimo: pedidos.ultimo
        };
    },

    fillPedido(state, pedido) {
        state.pedido = {
            cliente: pedido.cliente,
            configuracion: pedido.configuracion,
            detalles: pedido.detalles,
            detallesVentas: pedido.detallesVentas,
            pedido: pedido.presupuesto
        };
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
                .get("/api/pedidos", { params: params })
                .then(response => {
                    commit("fillPedidos", response.data);
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
                .get("/api/pedidos/" + params.id, { params: params })
                .then(response => {
                    commit("fillPedido", response.data);
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
                .post("/api/pedidos", state.form)
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

    edit: async function({ commit, dispatch }, params) {
        let pedido = await dispatch("show", params);
        let newForm = {
            id: pedido.presupuesto.id,
            bonificacion: pedido.presupuesto.bonificacion,
            cliente_id: pedido.presupuesto.cliente_id,
            cliente: pedido.cliente.razonsocial,
            condicion: "CUENTA CORRIENTE",
            comprobanteadherido: pedido.presupuesto.comprobanteadherido,
            confirmacion: pedido.presupuesto.numventa ? true : false,
            cotizacion: pedido.presupuesto.cotizacion,
            detalles: pedido.detalles,
            fecha: pedido.presupuesto.fecha,
            fechaCotizacion: pedido.presupuesto.fechaCotizacion,
            pedidoadherido: pedido.presupuesto.comprobanteadherido,
            observaciones: pedido.presupuesto.observaciones,
            recargo: pedido.presupuesto.recargo,
            subtotal: pedido.presupuesto.subtotal,
            total: pedido.presupuesto.total,
            totalPesos: pedido.presupuesto.totalPesos
        };
        commit("fillForm", newForm);
    },

    update({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .put("/api/pedidos/" + state.form.id, state.form)
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

    vender({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/vender", params)
                .then(response => {
                    dispatch("index");
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

    // TODO: Preguntar si se van a eliminar o no.
    // destroy({ dispatch }, params) {
    //     return new Promise((resolve, reject) => {
    //         axios
    //             .delete("/api/pedidos/" + params.id)
    //             .then(response => {
    //                 resolve(response.data);
    //             })
    //             .catch(error => {
    //                 dispatch("errorHandle", error.response, {
    //                     root: true
    //                 });
    //                 reject(error.response.data);
    //             });
    //     });
    // }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
