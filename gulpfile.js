const { src, dest, parallel, task, watch } = require('gulp');

//utils
const sourcemaps = require('gulp-sourcemaps');
const concat = require('gulp-concat');
const gulpIf = require('gulp-if');
const plumber = require('gulp-plumber');
const size = require('gulp-size');
const through = require('through2');

//scss
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const inlineSvg = require('postcss-inline-svg'); // https://www.npmjs.com/package/postcss-inline-svg
const sortMediaQueries = require('postcss-sort-media-queries');
const scss = require('gulp-dart-sass');
const csso = require('gulp-csso');
const bulkSass = require('gulp-sass-bulk-import');

//js
const babel = require('gulp-babel');
const terser = require('gulp-terser');

//svg
const svgSprite = require('gulp-svg-sprite');
const cheerio = require('gulp-cheerio');
const replace = require('gulp-replace');

//server
const browserSync = require('browser-sync').create();

const buildConfig = require('./config')

const paths = {
	source_directory: '.',
	build_directory: '.',
  sourcemaps: './sourcemaps',
  get js() {
		return {
			src: `${this.source_directory}/js/src`,
			build: `${this.build_directory}/js/build`,
      watch: `${this.source_directory}/js/src/**/*.js`,
    }
	},
  get jsBundle() {
		return {
			src: this.js.src,
			build: this.js.build,
      watch: [`${this.js.src}/**/*.js`, `!${this.jsPages.watch}`],
		}
	},
  get jsPages() {
    return {
      src: `${this.js.src}/pages/*.js`,
      build: this.js.build,
      watch: `${this.js.src}/pages/*.js`,
    };
  },
	get scss() {
		return {
			src: `${this.source_directory}/scss`,
			build: `${this.build_directory}/css`,
			watch: `${this.build_directory}/scss/**/*.scss`,
		}
	},
};

const removeEmptyLines = () => {
	return through.obj(function(file, _encoding, callback) {
		let fileContent = file.contents.toString();
		if (fileContent !== null || fileContent !== '' || fileContent) {
			try {
				fileContent = fileContent.replace(/[\r\n]/gm, '') // remove 2 and more spaces in a row
				fileContent = fileContent.replace(/[\s]{2,}/gm, '') // remove empty lines and line breaks
				file.contents = Buffer.from(fileContent);
			} catch (err) {
				this.emit('error', new Error(`Something went wrong during removing empty lines! Error:\n${err.message}`));
			}
		}
		this.push(file);
		callback();
	})
}


const server = () => {
	browserSync.init({
		proxy: buildConfig.SITE_URL || 'http://test-wp.loc/',
		files: ['./**/*.php'],
		open: buildConfig.shouldOpenBrowser,
		notify: false,
    reloadOnRestart: true,
  });
}

const styles = () => {
	return src(`${paths.scss.src}/*.scss`)
		.pipe(plumber({
			errorHandler: function (err) {
				console.log('styles ', err.message);
				this.end();
			}
		}))
		.pipe(gulpIf(buildConfig.__DEV__, sourcemaps.init()))
		.pipe(bulkSass())
		.pipe(scss().on('error', scss.logError))
		.pipe(postcss([
			autoprefixer(),
			inlineSvg({ removeFill: true, removeStroke: true }),
			sortMediaQueries({
				sort: 'desktop-first'
			})
		]))
		.pipe(gulpIf(buildConfig.__PROD__, csso({ restructure: true })))
		.pipe(gulpIf(!buildConfig.__PROD__, sourcemaps.write(paths.sourcemaps)))
		.pipe(gulpIf(buildConfig.__PROD__, size({ showFiles: true, title: 'CSS' })))
		.pipe(dest(paths.scss.build))
		.pipe(gulpIf(buildConfig.serverEnabled, browserSync.stream()));
}

const scriptsBundle = () => {
	return src([
		`${paths.jsBundle.src}/common/config.js`,
		`${paths.jsBundle.src}/vendor/jquery.min.js`,
		`${paths.jsBundle.src}/vendor/jquery.magnific-popup.min.js`,
		`${paths.jsBundle.src}/common/bundle.js`,
	])
		.pipe(plumber({
			errorHandler: function(err) {
				console.log('scriptsBundle ', err.message);
				this.end();
			}
		}))
		.pipe(gulpIf(buildConfig.__DEV__, sourcemaps.init()))
		.pipe(gulpIf(buildConfig.__PROD__, babel(buildConfig.babelOptions)))
		.pipe(gulpIf(buildConfig.__PROD__, terser(buildConfig.terserOptions)))
		.pipe(concat('bundle.js'))
		.pipe(gulpIf(buildConfig.__PROD__, removeEmptyLines()))
		.pipe(gulpIf(!buildConfig.__PROD__, sourcemaps.write(paths.sourcemaps)))
		.pipe(gulpIf(buildConfig.__PROD__, size({ showFiles: true, title: 'JS' })))
		.pipe(dest(paths.js.build))
		.pipe(gulpIf(buildConfig.serverEnabled, browserSync.stream()));
}

const scriptsPages = () => {
	return src(paths.jsPages.src)
		.pipe(plumber({
			errorHandler: function(err) {
				console.log('scriptsPages ', err.message);
				this.end();
			}
		}))
		.pipe(gulpIf(buildConfig.__DEV__, sourcemaps.init()))
		.pipe(gulpIf(buildConfig.__PROD__, babel(buildConfig.babelOptions)))
		.pipe(gulpIf(buildConfig.__PROD__, terser(buildConfig.terserOptions)))
		.pipe(gulpIf(buildConfig.__PROD__, removeEmptyLines()))
		.pipe(gulpIf(!buildConfig.__PROD__, sourcemaps.write(paths.sourcemaps)))
		.pipe(gulpIf(buildConfig.__PROD__, size({ showFiles: true, title: 'JS' })))
		.pipe(dest(paths.js.build))
		.pipe(gulpIf(buildConfig.serverEnabled, browserSync.stream()));
}

const createSvgSprite = () => {
	return src('./images/svg/sprite/*.svg')
		.pipe(plumber({
			errorHandler: function (err) {
				console.log(err.message);
				this.end();
			}
		}))
		// remove all fill, style and stroke declarations in out shapes
		.pipe(cheerio({
			run: function ($) {
				$('[fill]').removeAttr('fill');
				$('[stroke]').removeAttr('stroke');
				$('[style]').removeAttr('style');
			},
			parserOptions: {xmlMode: true}
		}))
		// cheerio plugin create unnecessary string '&gt;', so replace it.
		.pipe(replace('&gt;', '>'))
		.pipe(svgSprite({
			mode: {
				symbol: {
					prefix: '.svg-icon-%s',
					dimensions: '%s',
					sprite: '../sprite.svg',
					render: {
						scss: {
							dest:'../../../scss/components/_sprite.scss',
						}
					}
				}
			},
		}))
		.pipe(dest('./images/svg'));
};

const watchTask = () => {
	watch(paths.scss.watch, styles);
  if (buildConfig.useGulpForJs) {
    watch(`${paths.js.src}/common/*.js`, scriptsBundle);
    watch(`${paths.js.src}/pages/*.js`, scriptsPages);
  } else {
    watch(paths.js.watch).on("change", () =>
      setTimeout(browserSync.reload, 300)
    ); // reload while webpack bundling js
  }
}

const defaultTask = buildConfig.serverEnabled ? parallel(watchTask, server) : watchTask;

const build = buildConfig.useGulpForJs
  ? parallel(styles, scriptsBundle, scriptsPages)
  : styles;

task('default', defaultTask);
task('server', server);
task('sprite', createSvgSprite);
task('css', styles);
if (buildConfig.useGulpForJs) {
  task('js', parallel(scriptsBundle, scriptsPages));
}
task('build', build);
