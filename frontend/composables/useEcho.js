import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export function useEcho() {
    const {public: {baseURL}} = useRuntimeConfig();

    const token = useCookie('token');

    const echo = ref(null);

    onMounted(() => {
        if (!echo.value) {
            const url = new URL(baseURL).hostname;
            const wsHost = url.includes("client-manager.test")
                ? 'localhost'
                : new URL(baseURL).hostname;

            echo.value = new Echo({
                broadcaster: 'reverb',
                key: 'csf7pk9cj8ezjgbq9neh',
                wsHost: wsHost,
                wsPort: 8080,
                wssPort: 443,
                forceTLS: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: `${baseURL.replace(/\/api$/, '')}/broadcasting/auth`,
                auth: {
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                },
            });
        }
    });

    return echo;
}
