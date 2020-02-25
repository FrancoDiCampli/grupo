export function notificationHandle(store) {
    store.subscribeAction({
        after: action => {
            if (action.type != "notificaciones/index") {
                store.dispatch("notificaciones/index", {}, { root: true });
            }
        }
    });
}
