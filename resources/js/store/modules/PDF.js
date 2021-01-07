const actions = {
    printPedido({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/presupuestosPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "pedido_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printVenta({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/ventasPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "remito_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printEntrega({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/entregasPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "entrega_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printCompra({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/comprasPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "compra_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printRecibo({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/recibosPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "recibo_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printConsignacion({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/consignacionesPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "consignacion_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printDevolucion({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/devolucionesPDF/" + params.id,
                method: "GET",
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "devolucion_" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response);
                });
        });
    },

    printResumenCuenta({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios({
                url: "/api/resumenCuentaPDF",
                method: "POST",
                data: params,
                responseType: "blob"
            })
                .then(response => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "resumenCuenta.pdf");
                    document.body.appendChild(link);
                    link.click();
                    resolve();
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
    actions
};
