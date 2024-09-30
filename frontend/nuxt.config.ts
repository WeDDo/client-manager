// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    app: {
        head: {
            charset: 'utf-8',
            viewport: 'width=device-width, initial-scale=1',
        }
    },
    compatibilityDate: '2024-04-03',
    devtools: {enabled: true},
    runtimeConfig: {
        public: {
            baseURL: process.env.API_URL || 'http://client-manager.test/api',
        },
    },
    modules: [
        'nuxt-primevue',
        '@pinia/nuxt',
        '@vee-validate/nuxt',
        '@nuxt/eslint'
    ],
    primevue: {
        /* Options */
    }
})
