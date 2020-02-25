export function notificationHandle(store) {
    store.subscribeAction({
        after: action => {
            console.log(action.type);
            if (
                action.type != "notificaciones/index" &&
                action.type != "auth/deleteAuthData" &&
                action.type != "auth/logout"
            ) {
                store.dispatch("notificaciones/index", {}, { root: true });
            }
        }
    });
}
