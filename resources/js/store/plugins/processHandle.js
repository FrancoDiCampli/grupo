export function processHandle(store) {
    store.subscribeAction({
        before: action => {
            if (action.type != "notificaciones/index") {
                store.commit("iterateProcess", true);
            }
        },
        after: action => {
            if (action.type != "notificaciones/index") {
                store.commit("iterateProcess", false);
            }
        }
    });
}
