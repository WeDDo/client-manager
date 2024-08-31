export function useAuthHelper() {
    const mainStore = useMainStore();

    function setUserInLocalStorage(data) {
        localStorage.setItem('user', JSON.stringify(data));
        localStorage.setItem('token', data.token);
        mainStore.user = data;
        mainStore.token = data.token;
        mainStore.settings = data.settings;
    }

    function removeUserFromLocalStorage() {
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        mainStore.user = null;
        mainStore.token = null;
    }

    function getToken() {
        return mainStore.token;
    }

    function getTokenFromLocalStorage() {
        return localStorage.getItem('token');
    }

    function getAuthUser() {
        return mainStore.user;
    }

    function getAuthUserFromLocalStorage() {
        return JSON.parse(localStorage.getItem('user'));
    }

    return {
        setUserInLocalStorage,
        removeUserFromLocalStorage,
        getToken,
        getAuthUser,
        getTokenFromLocalStorage,
        getAuthUserFromLocalStorage
    };
}
