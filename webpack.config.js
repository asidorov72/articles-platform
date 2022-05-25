const Encore = require('@symfony/webpack-encore');

// if (!Encore.isRuntimeEnvironmentConfigured()) {
//     Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
// }

Encore
    // directories where the compiled styles will live.
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // clean output and say we are not on prod
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .enableStimulusBridge(
        './assets/controllers.json'
    )

    // from where .css and .js files should be taken
    .addEntry('app', [
        './assets/app.js',
        './node_modules/bootstrap/dist/js/bootstrap.min.js'
    ])
    .addStyleEntry('css/app', [
        './assets/styles/app.css',
        './node_modules/bootstrap/dist/css/bootstrap.min.css'
    ])
;
// Export the module
module.exports = Encore.getWebpackConfig();