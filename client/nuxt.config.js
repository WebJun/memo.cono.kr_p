require('dotenv').config()
const { join } = require('path')
const { copySync, removeSync } = require('fs-extra')

module.exports = {
  ssr: false,

  srcDir: __dirname,

  env: {
    apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
    appName: process.env.APP_NAME || 'Memo',
    appLocale: process.env.APP_LOCALE || 'ko',
    githubAuth: !!process.env.GITHUB_CLIENT_ID
  },

  head: {
    title: process.env.APP_NAME,
    // titleTemplate: '%s - ' + process.env.APP_NAME,
    titleTemplate: process.env.APP_NAME,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'Memo' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      {
        rel: 'stylesheet',
        type: 'text/css',
        href:
          'https://cdn.rawgit.com/innks/NanumSquareRound/master/nanumsquareround.min.css'
      }
    ]
  },

  loading: { color: '#007bff' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  css: [
    { src: '~assets/sass/app.scss', lang: 'scss' }
  ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/vform',
    '~plugins/axios',
    '~plugins/axios2',
    '~plugins/fontawesome',
    '~plugins/nuxt-client-init',
    '~plugins/vue-alertify',
    '~plugins/vue-cookies',
    { src: '~plugins/bootstrap', mode: 'client' }
  ],

  modules: [
    '@nuxtjs/router',
    '@nuxtjs/axios',
    '@nuxtjs/pwa',
  ],

  build: {
    extractCSS: true
  },

  hooks: {
    generate: {
      done (generator) {
        // Copy dist files to public/_nuxt
        if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
          const publicDir = join(generator.nuxt.options.rootDir, 'public', '_nuxt')
          removeSync(publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          removeSync(generator.nuxt.options.generate.dir)
        }
      }
    }
  },

  manifest: {
    name: 'Memo',
    short_name: 'Memo',
    start_url: '/',
    display: 'standalone',
    background_color: '#00a2ff',
  },

  workbox: {
    offline: false,
    runtimeCaching: [
      {
        urlPattern: "/*",
        handler: "networkFirst",
        method: "GET"
      }
    ]
  },
}
