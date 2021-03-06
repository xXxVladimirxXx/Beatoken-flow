const mix = require('laravel-mix');

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

mix
    .webpackConfig({
        resolve: {
            fallback: {
                "http": require.resolve("stream-http"),
                "https": require.resolve("https-browserify")
            }
        },
    })
    .js('resources/assets/js/app.js', 'public/assets/js')
    .version()
    .sass('resources/assets/sass/app.scss', 'public/assets/css')
    .version()
    .vue()
