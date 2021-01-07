const state = {
    unread: [],
    read: []
};

const mutations = {
    fillUnread(state, unread) {
        state.unread = unread;
    },
    fillRead(state, read) {
        state.read = read;
    }
};

const actions = {
    index({ commit }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/notificaciones", { params: params })
                .then(response => {
                    let unread = response.data.notificaciones.noLeidas;
                    let read = response.data.notificaciones.leidas;
                    commit("fillUnread", unread);
                    commit("fillRead", read);
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response);
                });
        });
    },

    markRead({ dispatch }, params) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/notificaciones/" + params.id)
                .then(response => {
                    dispatch("index");
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response);
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
