const path = require('path')
const webpack = require('webpack')
const autoprefixer = require('autoprefixer')
const FileManagerPlugin = require('filemanager-webpack-plugin')
const CompressionPlugin = require('compression-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')

const isProduction = process.env.NODE_ENV === 'production'

const mode = isProduction ? 'production' : 'development'
const devtool = isProduction ? 'source-map' : 'source-map'
const jsFilename = isProduction ? 'aokranj.min.js' : 'aokranj.js'
const cssFilename = isProduction ? 'aokranj.min.css' : 'aokranj.css'

const paths = {
  entry: path.resolve(__dirname, 'src', 'aokranj.js'),
  output: path.resolve(__dirname, 'public'),
  src: path.resolve(__dirname, 'src'),
  dist: path.resolve(__dirname, 'dist'),
}

const config = {
  mode,
  entry: paths.entry,
  output: {
    filename: jsFilename,
    path: paths.output
  },
  devtool,
  module: {
    rules: [{
      test: /\.js$/,
      include: paths.src,
      use: [{ loader: 'babel-loader' }],
    },{
      test: /\.(css|scss)$/,
      use: [
        MiniCssExtractPlugin.loader,
        'css-loader',
        'postcss-loader',
        'sass-loader',
      ],
    },{
      test: /\.(png|jpg|svg|gif|woff2|woff|eot|ttf)$/,
      loader: 'file-loader',
    }],
  },
  plugins: [
    new webpack.NamedModulesPlugin(),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      Popper: ['popper.js', 'default'],
    }),
    new MiniCssExtractPlugin({
      filename: cssFilename,
    }),
  ],
  optimization: {
    namedModules: true,
    noEmitOnErrors: true,
    concatenateModules: true,
    /*
    minimizer: [
      new UglifyJsPlugin(),
    ],
    */
  }
}

if (isProduction) {
  config.plugins.push(
    new CompressionPlugin({
			asset: '[path].gz[query]',
			algorithm: 'gzip',
			test: /\.(js)$/,
			threshold: 10240,
			minRatio: 0.8
		}),
    new FileManagerPlugin({
      onStart: {
        delete: [
          paths.dist,
        ],
      },
      onEnd: {
        copy: [
          { source: './images/**/*', destination: './dist/images/' },
          { source: './inc/**/*', destination: './dist/inc/' },
          { source: './languages/**/*', destination: './dist/languages/' },
          { source: './public/**/*', destination: './dist/public/' },
          { source: './templates/**/*', destination: './dist/templates/' },
          //{ source: './vendor/**/*', destination: './dist/vendor/' },
          { source: './*.php', destination: './dist/' },
          { source: './manifest.json', destination: './dist/manifest.json' },
          { source: './screenshot.jpg', destination: './dist/screenshot.jpg' },
          { source: './style.css', destination: './dist/style.css' },
        ],
      },
    }),
  )
} else {
  config.plugins.push(
    new webpack.HotModuleReplacementPlugin(),
    new BrowserSyncPlugin({
      proxy: 'http://aokranj.local/',
      host: 'localhost',
      port: 3000,
      injectCss: true,
      files: [
        './*.php',
        './inc/**/*.php',
        './templates/**/*.php',
        './languages/*.po',
        './public/*.css',
        './public/*.js',
      ],
    },{
      reload: false,
    }),
  )
}

module.exports = config
