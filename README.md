# Wordpress theme template

## If Wordpress bootstrapped with [bedrock](https://roots.io/bedrock/):
1. go to project root
2. run `composer require filp/whoops kint-php/kint --dev`

* [The 7-1 Pattern](https://sass-guidelin.es/#the-7-1-pattern) - css architecture used in project.


## PHP
- [Kint](https://github.com/kint-php/kint) lib used to better debugging
- [whoops](https://github.com/filp/whoops) - error handler framework for PHP.
  Out-of-the-box, it provides a pretty error interface

## After clone template
- navigate to theme folder
- run `npm install`
- run `composer require filp/whoops kint-php/kint --dev` (if Wordpress was not bootstrapped with [bedrock](https://roots.io/bedrock/))
  
<br />

| Env variable | Options | Default | Description |
| ------ | ------ | ------ | ------ |
| `NODE_ENV` | development <br /> production | development | build process depends on this variable |
| `SITE_URL` | none | none | site url, that will be used as proxy for [browsersync](https://www.browsersync.io/) <br /> if not set, you can't start [dev server]((https://www.browsersync.io/)) and handle assets at all |
| `JS_BUILD_TOOL` | gulp <br /> webpack | gulp | tool for building and transpiling *js* |

## Usage

| Gulp task | Description |
| ------ | ------ |
| `server` | run [browsersync](https://www.browsersync.io/) |
| `sprite` | create `sprite.svg`(from svg files (*/build/img/svg/sprite*) and `_sprite.scss` (inside */dev/scss/modules*) |
| `css` | build styles |
| `js` | transpile *js* files |
| `build` | build html && css && js |

<br />

| CLI Flag | Description |
| ------ | ------ |
| `--s`, `--serve`, `--server` | enable [browsersync](https://www.browsersync.io/) |
| `--o`, `--open` | open browser, when [browsersync](https://www.browsersync.io/) started |
| `--p`, `--prod`, `--production` | with minified styles and scripts |
| `--d`, `--dev`, `--development` | without minifying styles and scripts |

<br />

| NPM command | Description |
| ------ | ------ |
| `watch:dev:gulp` | `gulp` development mode with enabled server [browsersync](https://www.browsersync.io/) |
| `build:dev:gulp` | `gulp` build without minified styles and scripts |
| `build:prod:gulp` | `gulp` build with minified styles and scripts |
| `watch:dev:webpack` | `webpack` development mode, watching files |
| `build:dev:webpack` | `webpack` development mode, bundling js without minification |
| `build:prod:webpack` | `webpack` bundling js with minification |

<br />

| Makefile commands | Description |
| ------ | ------ |
| `start` | start watching and server, mode `development` [browsersync](https://www.browsersync.io/) |
| `start-webpack` | start watching js with webpack, other assets with gulp and server, mode `development` [browsersync](https://www.browsersync.io/) |
| `dev` | remove `build` directory + building with `gulp`, mode `development` |
| `prod` | remove `build` directory + building with `gulp`, with minification |


## Hints
- edit `paths` object in *gulpfile.js* if needed
- make sure you specify correct webpack entry point in `webpack.config.js`
- `__DEV__` and `__PROD__` mode variables provided to JS during build time
