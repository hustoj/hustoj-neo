const mix = require('laravel-mix');

mix.disableNotifications();

mix.sass('resources/assets/sass/admin.scss', 'public/css')
    .options({
        processCssUrls: false,
        module: {
            rules: [
                {
                    test: /\.css$/,
                    loader: "style-loader!css-loader"
                }
            ]
        }
    })
    .js('resources/assets/js/app.js', 'public/admin/js')
    .extract(['vue', 'vue-router', 'vue-bus', 'element-ui', 'axios', 'lodash',
        'jquery', 'echarts', 'vue-cookie'], 'public/admin/js/vendor.js');
