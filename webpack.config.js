var Encore = require('@symfony/webpack-encore');
var path = require('path');
var eslintrc = require('./.eslintrc')
var DashboardPlugin = require('webpack-dashboard/plugin')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // Alias
    .addAliases({
      '@npm': path.resolve(__dirname, 'node_modules'),
      '@bundle': path.resolve(__dirname, 'public/bundles'),
      '@public': path.resolve(__dirname, 'public'),
      '@js': path.resolve(__dirname, 'assets/js'),
      '@css': path.resolve(__dirname, 'assets/css'),
    })

    /*
     * ENTRY CONFIG
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('create', './assets/js/create.js')
    .addEntry('waiting', './assets/js/waiting.js')
    .addEntry('game', './assets/js/game.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // ESLint
    .enableEslintLoader(options => {
      delete options.parser;
      return eslintrc
    })
    .configureLoaderRule('eslint', loaderRule => {
      loaderRule.test = /\.(jsx?|vue)$/
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    .enableLessLoader()
    .enableVueLoader()

    // Dashboard compiler
    .addPlugin(new DashboardPlugin())

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
