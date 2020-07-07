/*
 development (watch)
 gulp
 gulp --prod (minified && babel)

 production
 gulp build --prod (minified && babel)

 */

const { src, dest, parallel, task, watch } = require('gulp');

// utils
const sourcemaps = require('gulp-sourcemaps');
const concat = require('gulp-concat');
const gulpIf = require('gulp-if');
const fileinclude = require('gulp-file-include');

// scss
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const csso = require('gulp-csso');
const gcmq = require('gulp-group-css-media-queries');
const bulkSass = require('gulp-sass-bulk-import');

// js
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');

// svg
const svgSprite = require('gulp-svg-sprite');
const cheerio = require('gulp-cheerio');
const replace = require('gulp-replace');

// server
const browserSync = require('browser-sync').create();

const isProd = ['--p', '--prod', '--production'].some(item => process.argv.includes(item));
const browserSyncEnabled = ['--s', '--serve', '--server'].some(item => process.argv.includes(item));
const isDev = !isProd;

const server = () => {
	browserSync.init({
		proxy: "http://localhost/wordpress_cms/lamellatrading/",
		files: ['./*.php', './includes/*.php', './sections/*.php'],
		// server: {
		// 	baseDir: "./",
		// 	directory: false
		// },
		open: false,
		notify: false
	});
};

const styles = () => {
	return src('./scss/*.scss')
		.pipe(gulpIf(isDev, sourcemaps.init()))
		.pipe(bulkSass())
		.pipe(sass())
		.pipe(gcmq())
		.pipe(autoprefixer())
		.pipe(csso({ restructure: true }))
		.pipe(replace(/[\.\.\/]+images/gmi, '../images')) //заменяем пути к изображениям на правильные
		.pipe(gulpIf(isDev, sourcemaps.write()))
		.pipe(dest('./css'))
		.pipe(gulpIf(browserSyncEnabled, browserSync.stream()));
};

const jsCommon = () => {
	return src([
		'./js/src/vendor/jquery.min.js',
		'./js/src/common/config.js',
		'./js/src/common/lib.js',
		// './js/src/vendor/js.cookie.min.js',
		'./js/src/common/modals.js',
		// './js/src/vendor/jquery.maskedinput.min.js',
		'./js/src/vendor/swiper.min.js',
		'./js/src/common/common.js',
	])
    .pipe(fileinclude({
      prefix: '@@',
      basepath: '@file'
    }))
		.pipe(gulpIf(isDev, sourcemaps.init()))
		.pipe(gulpIf(isProd, babel({
			presets: ['@babel/env']
		})))
		.pipe(gulpIf(isProd, uglify()))
		.pipe(concat('bundle.js'))
		.pipe(gulpIf(isDev, sourcemaps.write()))
		.pipe(dest('./js/min'))
		.pipe(gulpIf(browserSyncEnabled, browserSync.stream()));
};

const jsPages = () => {
	return src(['./js/src/pages/*.js'])
    .pipe(fileinclude({
      prefix: '@@',
      basepath: '@file'
    }))
	.pipe(gulpIf(isDev, sourcemaps.init()))
	.pipe(gulpIf(isProd, babel({
		presets: ['@babel/env']
	})))
	.pipe(gulpIf(isProd, uglify()))
	.pipe(gulpIf(isDev, sourcemaps.write()))
	.pipe(dest('./js/min'))
	.pipe(gulpIf(browserSyncEnabled, browserSync.stream()));
};

const createSvgSprite = () => {
	return src('./images/svg/sprite/*.svg')
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
							dest:'../../../scss/modules/_sprite.scss',
						}
					}
				}
			},
		}))
		.pipe(dest('./images/svg'));
};

const watchTask = () => {
	watch('./scss/**/*.scss', styles);
	watch('./js/src/pages/*.js', jsPages);
	watch(['./js/src/common/*.js', './js/src/vendor/*.js'], jsCommon);
};

const buildTask = parallel(styles, jsPages, jsCommon);

const defaultTask = browserSyncEnabled ? parallel(watchTask, server) : watchTask;

task('js', parallel(jsPages, jsCommon));
task('css', styles);
task('sprite', createSvgSprite);
task('build', buildTask);
task('default', defaultTask);
