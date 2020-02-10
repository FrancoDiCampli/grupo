const actions = {
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
    }
};

export default {
    namespaced: true,
    actions
};
