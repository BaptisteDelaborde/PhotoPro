// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  css: [
    '@/assets/css/main.css',
  ],
  vite: {
    server: {
      allowedHosts: 'all',
    },
  },
  runtimeConfig: {
    public: {
      apiFrontofficeUrl: process.env.NUXT_PUBLIC_API_FRONTOFFICE_URL || 'http://localhost:8082',
      s3Url: process.env.NUXT_PUBLIC_S3_URL || 'http://localhost:8333',
    }
  }
})
