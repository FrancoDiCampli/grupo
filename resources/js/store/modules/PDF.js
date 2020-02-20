const actions = {
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
                    link.setAttribute("download", "venta" + params.id + ".pdf");
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
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
                        "consignacion" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
                });
        });
    },

    printPresupuesto({ dispatch }, params) {
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
                        "presupuesto" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
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
                        "compra" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                    reject(error.response.data);
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
                        "recibo" + params.id + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    resolve();
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
    actions
};
