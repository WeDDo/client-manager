// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2024-04-03',
    devtools: {enabled: true},
    runtimeConfig: {
        public: {
            baseURL: process.env.API_URL || 'http://client-manager.test/api',
        },
    },
    modules: [
        'nuxt-primevue'
    ],
    primevue: {
        /* Options */
    }
})
