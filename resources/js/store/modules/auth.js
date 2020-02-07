const state = {
    user: null,
    form: {},
    errors: null
};

const mutations = {
    fillUser(state, user) {
        state.user = user;
    },

    fillForm(state, form) {
        state.form = form;
    },

    resetForm(state) {
        state.form = {};
    },

    resetUser(state) {
        state.user = null;
    }
};

const actions = {
    login({ state, commit }) {
        return new Promise((resolve, reject) => {
            axios
                .get("/airlock/csrf-cookie")
                .then(() => {
                    axios
                        .post("/login", state.form)
                        .then(response => {
                            window.localStorage.setItem(
                                "logged",
                                JSON.stringify(true)
                            );
                            resolve(response.data);
                        })
                        .catch(error => {
                            commit("fillErrors", error.response, {
                                root: true
                            });
                            reject(error.response.data);
                        });
                })
                .catch(error => {
                    commit("fillErrors", error.response, {
                        root: true
                    });
                });
        });
    },

    user({ commit }) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user")
                .then(response => {
                    console.log(response.data);
                    commit("fillUser", response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    commit("iterateProcess", false, { root: true });
                    reject(error.response.data);
                });
        });
    },

    updateAccount({ commit }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/update_account", state.form)
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    commit("fillErrors", error.response, { root: true });
                    reject(error.response.data);
                });
        });
    },

    logout({ dispatch }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/logout")
                .then(() => {
                    dispatch("deleteAuthData");
                    resolve();
                })
                .catch(() => {
                    dispatch("deleteAuthData");
                    reject();
                });
        });
    },

    deleteAuthData: function({ commit }) {
        window.localStorage.removeItem("logged");
        commit("resetUser");
        commit("resetForm");
        // location.reload();
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
