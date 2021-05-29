const path = require('path');
const Webpack = require('webpack');
const WebpackBar = require('webpackbar');
const TerserPlugin = require("terser-webpack-plugin");

const buildConfig = {
  ...require('./config'),
  SOURCE_ROOT: path.join(process.cwd(), 'js/src'),
  BUILD_ROOT: path.join(process.cwd(), 'js/build'),
}

const shouldWatch = process.argv.includes('--watch');
const shouldBuild = process.argv.includes('--build');

const webpackConfig = {
  mode: buildConfig.MODE,
  entry: {
    bundle: path.join(buildConfig.SOURCE_ROOT, 'common/bundle.js'),
  },
  output: {
    path: buildConfig.BUILD_ROOT,
    filename: '[name].js',
    sourceMapFilename: 'sourcemaps/[name][ext].map', // works only if devtool='source-map'
    publicPath: '/',
  },
  devtool: buildConfig.__DEV__ ? 'eval-cheap-module-source-map' : false,
  target: 'web',
  resolve: {
    extensions: ['.js', '.json'],
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: {
          loader: 'babel-loader',
          options: buildConfig.babelOptions,
        },
        exclude: /node_modules/,
      },
    ],
  },
  optimization: buildConfig.__PROD__ ? {
    nodeEnv: buildConfig.MODE,
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
        terserOptions: buildConfig.terserOptions,
      })
    ],
  } : {},
  plugins: [
    new Webpack.DefinePlugin({
      __DEV__: buildConfig.__DEV__,
      __PROD__: buildConfig.__PROD__,
    }),
    new WebpackBar({}),
  ],
};


const compiler = Webpack(webpackConfig);

const compilerErrorHandler = (err, stats) => {
  if (err) {
    console.error(err.stack || err);
    if (err.details) {
      console.error(err.details);
    }
    return;
  }

  const info = stats.toJson();

  if (stats.hasErrors()) {
    console.error(info.errors);
  }

  if (stats.hasWarnings()) {
    console.warn(info.warnings);
  }
};

const watch = () => {
  return compiler.watch({
    aggregateTimeout: 300,
    ignored: /node_modules/,
  }, compilerErrorHandler);
}

const build = () => {
  return compiler.run(compilerErrorHandler);
}

if (shouldWatch) {
  watch();
} else if(shouldBuild) {
  build();
} else {
  console.error('Missing flag "--watch" or "--build"');
}
