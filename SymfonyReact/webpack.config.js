var Encore = require('@symfony/webpack-encore');

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

    //NEW REACT APP BITS
    .enableReactPreset()
    .addEntry('react-app', './assets/js/ReactApp.js')


    //base.twig
    .addEntry('bootstrap4', 'bootstrap/dist/css/bootstrap.css')
    .addEntry('font-awesome', '@fortawesome/fontawesome-free/css/all.css')
    // .addEntry('font-awesome-brands', '@fortawesome/fontawesome-free/css/brands.css')
    // .addEntry('font-awesome-solid', '@fortawesome/fontawesome-free/css/solid.css')
    .addEntry('Appcss', './assets/css/App.css')
    .addEntry('sb-admin2', 'startbootstrap-sb-admin-2/css/sb-admin-2.css')

    // .addEntry('font-awesome-all-js', '@fortawesome/fontawesome-free/js/all.js')
    // .addEntry('font-awesome-js', '@fortawesome/fontawesome-free/js/fontawesome.js')
    // .addEntry('font-awesome-brands-js', '@fortawesome/fontawesome-free/js/brands.js')
    // .addEntry('font-awesome-regular-js', '@fortawesome/fontawesome-free/js/regular.js')
    // .addEntry('font-awesome-solid-js', '@fortawesome/fontawesome-free/js/solid.js')
    // .addEntry('font-awesome-shims-js', '@fortawesome/fontawesome-free/js/v4-shims.js')
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // .enableReactPreset()
    /*.enableReactPreset()
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

    .configureBabel(function(babelConfig) {
        babelConfig.plugins.push('@babel/plugin-proposal-class-properties');
    })
    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    // .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
