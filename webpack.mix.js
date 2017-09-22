let mix = require('laravel-mix');

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
var webpack = require('webpack')
mix.webpackConfig({
    plugins: [
        new webpack.NormalModuleReplacementPlugin(/element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/, 'element-ui/lib/locale/lang/en')
    ]
});
console.log('process.env.NODE_ENV',process.env.NODE_ENV);
mix.js('resources/assets/iframe.js', 'public/js/iframe.js')
    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css');
if (process.env.NODE_ENV=='production') {
    console.log('mix.config.inProduction',process.env.NODE_ENV);
    mix.version();
}

