const path = require('path');
const fs = require('fs');
const dotenv = require('dotenv');

const envPath = path.join(process.cwd(), '.env');

try {
  if (!fs.existsSync(envPath)) {
    console.error('".env" file not found, create one!');
    process.exit(1);
  }
} catch(err) {
  console.error(err)
}

const envConfig = dotenv.config({
  path: envPath,
});
if (envConfig.error) {
  throw envConfig.error;
}

const ENV = envConfig.parsed

const MODE = process.env.NODE_ENV || 'development';
const __PROD__ = (MODE === 'production') || ['--p', '--prod', '--production'].some(item => process.argv.includes(item));
const __DEV__ = (MODE === 'development') || ['--d', '--dev', '--development'].some(item => process.argv.includes(item));

const useGulpForJs = (ENV.JS_BUILD_TOOL || 'gulp') === 'gulp';

const serverEnabled = ['--s', '--serve', '--server'].some(item => process.argv.includes(item));
const shouldOpenBrowser = serverEnabled && ['--o', '--open'].some(item => process.argv.includes(item));

const terserOptions = {
  parse: {
    html5_comments: false
  },
  mangle: true,
  sourceMap: false, // false by default. Handled by 'gulp-sourcemaps'.
  compress: {
    defaults: true,
    drop_console: false, // false by default. Pass true to discard calls to console.* functions.
    keep_infinity : true, // false by default. Pass true to prevent Infinity from being compressed into 1/0, which may cause performance issues on Chrome.
    passes: 2, // 1 by default. The maximum number of times to run compress.
  },
  format: {
    comments: false, // "some" by default
    preamble : null, // null by default. When passed it must be a string and it will be prepended to the output literally. The source map will adjust for this text. Can be used to insert a comment containing licensing information, for example.
    quote_style: 3, // 0 by default. 3 - always use the original quotes.
    preserve_annotations: false, // false by default.
    ecma: 2019, // 5 by default. Desired EcmaScript standard version for output.
  },
  ecma: 2019, // 5 by default. Desired EcmaScript standard version for output.
  keep_classnames: false, // undefined by default.
  keep_fnames: false, // false by default.
  safari10: false, // false by default.
};

module.exports = {
  ENV,
  MODE,
  __PROD__,
  __DEV__,
  serverEnabled,
  shouldOpenBrowser,
  useGulpForJs,
  terserOptions,
};
