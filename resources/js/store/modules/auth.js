import Vue from "vue";

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
    login({ state, dispatch }) {
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
                            dispatch("errorHandle", error.response, {
                                root: true
                            });
                            reject(error.response.data);
                        });
                })
                .catch(error => {
                    dispatch("errorHandle", error.response, {
                        root: true
                    });
                });
        });
    },

    user({ commit, dispatch }) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user")
                .then(response => {
                    commit("fillUser", response.data);
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

    updateAccount({ dispatch }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/update_account", state.form)
                .then(response => {
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

        Vue.prototype.$user.set({
            rol: "not_authorized",
            permissions: []
        });
        // location.reload();
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
