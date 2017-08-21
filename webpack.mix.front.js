let { mix } = require('laravel-mix');

mix.disableNotifications();

mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.css$/,
                loader: "style-loader!css-loader"
            },
        ]
    }
});

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
mix.js('resources/assets/front/app.js', 'public/js')
    .extract(['nprogress', 'lodash', 'jquery'], 'public/js/vendor.js')
    .sass('resources/assets/sass/app.scss', 'public/css');

mix.scripts([
    'resources/assets/front/bootstrap-hover-dropdown.min.js',
], 'public/js/libs.js');

// mix
mix.copy('resources/assets/sass/font-awesome.min.css', 'public/css/font-awesome.min.css');
mix.copy('resources/assets/fonts', 'public/fonts');