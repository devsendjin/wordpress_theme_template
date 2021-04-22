# Wordpress theme template

* [The 7-1 Pattern](https://sass-guidelin.es/#the-7-1-pattern) - css architecture used in project.


## PHP
- [Kint](https://github.com/kint-php/kint) lib used to better debugging
- [whoops](https://github.com/filp/whoops) - error handler framework for PHP.
  Out-of-the-box, it provides a pretty error interface

## After clone template
- run `npm install && composer install`

  <br />

| ENV | Description |
| ------ | ------ |
| `SITE_URL` | site url, that will be used as proxy for [browsersync](https://www.browsersync.io/) (default: `http://test-wp.loc/`) |
| `JS_BUILD_TOOL` | tool for building and transpiling *js* (possible values: `gulp`, `webpack`)  |

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
| `watch:dev` | development mode with enabled server [browsersync](https://www.browsersync.io/) |
| `watch:prod` | production mode with enabled server [browsersync](https://www.browsersync.io/) |
| `build:dev` | build without minified styles and scripts
| `build:prod` | build with minified styles and scripts
| `watch:webpack` | development using webpack |
| `build:webpack` | production build using webpack |

## Hints
- 

## Hints
- edit `paths` object in *gulpfile.js* if needed
- before using gulp, edit scripts src in `scriptsBundle` and `scriptsPages` functions
- before using webpack configure entry point in `webpack.config.js`
- `__DEV__` and `__PROD__` mode variables provided to JS during build time
