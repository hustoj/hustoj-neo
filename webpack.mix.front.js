const mix = require('laravel-mix');

mix.disableNotifications();

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
// front
mix.js(['resources/assets/front/app.js'], 'public/js')
    .extract(['lodash', 'jquery', 'bootstrap', 'bootstrap-dropdown-hover'], 'public/js/vendor.js')
    .sass('resources/assets/sass/app.scss', 'public/css');

mix.webpackConfig(webpack => {
    return {
        plugins: [
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
            })
        ]
    }
});

// mix
mix.copy('resources/assets/sass/font-awesome.min.css', 'public/css/font-awesome.min.css');
mix.copy('resources/assets/fonts', 'public/fonts');
