const state = {
    users: null,
    form: {}
};

const mutations = {
    fillUsers(state, users) {
        state.users = users;
    },

    fillForm(state, form) {
        state.form = form;
    },

    resetForm(state) {
        state.form = {};
    }
};

const actions = {
    index: function({ commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/users", { params: params })
                .then(response => {
                    commit("fillUsers", response.data);
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

    save: function({ state, commit, dispatch }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/users", state.form)
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

    edit: function({ commit }, params) {
        commit("fillForm", params.data);
    },

    update: function({ state, commit, dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .put("/api/users/" + params.id, state.form)
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

    destroy: function({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .delete("/api/users/" + params.id)
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
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
