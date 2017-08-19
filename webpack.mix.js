const { mix } = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
    .extract(['vue', 'vue-router', 'vue-bus', 'element-ui', 'axios', 'lodash', 
        'jQuery', 'echarts'])
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/admin.scss', 'public/css');

mix.copy('resources/assets/sass/font-awesome.min.css', 'public/css/font-awesome.min.css');
mix.copy('resources/assets/fonts', 'public/fonts');