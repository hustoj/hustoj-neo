/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const mix = require('laravel-mix');

// webpackbar is only used for console progress output and is incompatible
// with current webpack 5 ProgressPlugin option validation.
mix.override(config => {
    config.plugins = config.plugins.filter(
        plugin => plugin.constructor.name !== 'WebpackBarPlugin'
    );
});

if (process.env.section) {
    require(`${__dirname}/webpack.mix.${process.env.section}.js`);
}
