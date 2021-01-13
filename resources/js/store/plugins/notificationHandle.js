export function notificationHandle(store) {
    store.subscribeAction({
        after: action => {
            if (action.type == "entregas/entregar" || action.type == "entregas/destroy") {
                store.dispatch("notificaciones/index", {}, { root: true });
            }
        }
    });
}
