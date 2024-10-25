import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export function useEcho() {
    const token = useCookie('token');

    const echo = ref(null);

    onMounted(() => {
        if (!echo.value) {
            echo.value = new Echo({
                broadcaster: 'reverb',
                key: 'csf7pk9cj8ezjgbq9neh',
                wsHost: 'localhost',
                wsPort: 8080,
                wssPort: 443,
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: 'http://client-manager.test/broadcasting/auth',
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
