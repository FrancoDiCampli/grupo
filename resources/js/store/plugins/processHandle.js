export function processHandle(store) {
    store.subscribeAction({
        before: () => {
            store.commit("iterateProcess", true);
        },
        after: () => {
            store.commit("iterateProcess", false);
        }
    });
}
