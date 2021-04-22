const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require("terser-webpack-plugin");

const config = {
  ...require('./scripts.config'),
  SOURCE_ROOT: path.join(process.cwd(), 'js/src'),
  BUILD_ROOT: path.join(process.cwd(), 'js/build'),
}

module.exports = function (env, argv) {
  return {
    mode: config.MODE,
    entry: path.join(config.SOURCE_ROOT, 'common/bundle.js'),
    output: {
      path: config.BUILD_ROOT,
      filename: 'bundle.js',
      sourceMapFilename: 'sourcemaps/[name][ext].map', // works only if devtool='source-map'
      publicPath: '/',
    },
    devtool: config.__DEV__ ? 'eval-cheap-module-source-map' : false,
    target: 'web',
    resolve: {
      extensions: ['.js', '.json'],
    },
    watchOptions: {
      aggregateTimeout: 600,
      ignored: /node_modules/,
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
              plugins: ['@babel/plugin-proposal-class-properties'],
            },
          },
          exclude: /node_modules/,
        },
      ],
    },
    optimization: config.__PROD__ ? {
      nodeEnv: config.MODE,
      runtimeChunk: false,
      minimize: true,
      sideEffects: true,
      concatenateModules: true,
      emitOnErrors: false,
      removeEmptyChunks: true,
      mergeDuplicateChunks: true,
      removeAvailableModules: true,
      providedExports: true,
      usedExports: true,
      minimizer: [
        new TerserPlugin({
          exclude: /node_modules/,
          extractComments: false,
          terserOptions: config.terserOptions,
        })
      ],
    } : {},
    plugins: [
      new webpack.DefinePlugin({
        __DEV__: config.__DEV__,
        __PROD__: config.__PROD__,
      }),
    ],
  };
};
