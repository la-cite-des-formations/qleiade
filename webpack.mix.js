const mix = require('laravel-mix');
const path = require('path');
const dotenv = require('dotenv');
const webpack = require('webpack');

const nodeEnv = process.env.NODE_ENV
console.log("env : ", nodeEnv);

var envFileName = nodeEnv;
if (nodeEnv === "production") {
    if (process.env.npm_config_preprod === "1") {
        //c'est pour le distant preprod
        console.log("for preprod env file")
        envFileName = "preproduction"
    } else {
        console.log("for prod env file")
        envFileName = "production"
    }
}

const env = dotenv.config({ path: path.resolve(__dirname, 'api_front/public/app/.env.' + envFileName) }).parsed;

// create a nice object from the env variable
const envKeys = Object.keys(env).reduce((prev, next) => {
    prev[`process.env.${next}`] = JSON.stringify(env[next]);
    return prev;
}, {});

let ImageminPlugin = require('imagemin-webpack-plugin').default;



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

mix.js('resources/js/app.jsx', 'public/js')
    .react()
    .sass('resources/sass/app.scss', 'public/css')
    .copyDirectory('resources/images', 'public/images');

mix.webpackConfig({
    resolve: {
        alias: {
            // '@': path.resolve(__dirname, 'resources/js'),
            '@app': path.resolve(__dirname, 'api_front/public/app'),
            '@admin_panel': path.resolve(__dirname, 'admin_panel/public/js'),
            '@components': path.resolve(__dirname, 'api_front/public/app/components'),
            '@parts': path.resolve(__dirname, 'api_front/public/app/parts'),
            '@services': path.resolve(__dirname, 'api_front/public/app/services'),
            '@factory': path.resolve(__dirname, 'api_front/public/app/services/factory'),
            '@store': path.resolve(__dirname, 'api_front/public/app/store'),
            '@views': path.resolve(__dirname, 'api_front/public/app/views'),
            '@mypackages': path.resolve(__dirname, 'api_front/public/app/services')
        }
    },
    module: {
        rules: [
            {
                test: /\.(jsx|js|vue)$/,
                loader: 'eslint-loader',
                enforce: 'pre',
                exclude: /(node_modules)/,
                options: {
                    formatter: require('eslint-friendly-formatter')
                }
            }
        ]
    },
    plugins: [
        new webpack.DefinePlugin(envKeys),
        new ImageminPlugin({
            //            disable: process.env.NODE_ENV !== 'production', // Disable during development
            pngquant: {
                quality: '95-100',
            },
            test: /\.(jpe?g|png|gif|svg)$/i,
        }),
    ],
})
