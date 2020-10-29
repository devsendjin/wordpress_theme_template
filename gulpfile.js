const { src, dest, parallel, task, watch, lastRun } = require('gulp');
const webpackStream = require('webpack-stream');

//utils
const sourcemaps = require('gulp-sourcemaps');
const gulpIf = require('gulp-if');
const plumber = require('gulp-plumber');
const size = require('gulp-size');
const remember = require('gulp-remember');

//scss
const postcss = require('gulp-postcss');
const inlineSvg = require('postcss-inline-svg'); // https://www.npmjs.com/package/postcss-inline-svg
const sortMediaQueries = require('postcss-sort-media-queries');
const scss = require('gulp-sass');
const csso = require('gulp-csso');
const bulkSass = require('gulp-sass-bulk-import');

//js
const TerserPlugin = require('terser-webpack-plugin');

//svg
const svgSprite = require('gulp-svg-sprite');
const cheerio = require('gulp-cheerio');
const replace = require('gulp-replace');

//server
const browserSync = require('browser-sync').create();

const MODE = process.env.NODE_ENV || 'development';
const isProduction = (process.env.NODE_ENV === 'production') || ['--p', '--prod', '--production'].some(item => process.argv.includes(item));
const isDevelopment = (process.env.NODE_ENV === 'development') || ['--d', '--dev', '--development'].some(item => process.argv.includes(item));

const serverEnabled = ['--s', '--serve', '--server'].some(item => process.argv.includes(item));
const shouldOpenBrowser = serverEnabled && ['--o', '--open'].some(item => process.argv.includes(item));

const server = () => {
	browserSync.init({
		proxy: "http://test-wp.loc/",
		files: ['./**/*.php'],
		open: shouldOpenBrowser,
		notify: false
	});
}

const styles = () => {
	return src('./scss/*.scss')
		.pipe(plumber({
			errorHandler: function (err) {
				console.log('styles ', err.message);
				this.end();
			}
		}))
		.pipe(gulpIf(isDevelopment, sourcemaps.init()))
		.pipe(bulkSass())
		.pipe(scss().on('error', scss.logError))
		.pipe(postcss([
			require('autoprefixer')(),
			inlineSvg({ removeFill: true, removeStroke: true }),
			sortMediaQueries({
				sort: 'desktop-first'
			})
		]))
		.pipe(csso({ restructure: true }))
		// .pipe(replace(/[\.\.\/]+img/gmi, '../img')) //заменяем пути к изображениям на правильные
		// .pipe(replace(/url\(["']?(?:\.?\.?\/?)*(?:\w*\/)*(\w+)(.svg|.gif|.png|.jpg|.jpeg)["']?\)/gmi, '"../img/$1$2"')) //заменяем пути к изображениям на правильные
		.pipe(gulpIf(isDevelopment, sourcemaps.write()))
		.pipe(gulpIf(isProduction, size({ showFiles: true, title: 'CSS' })))
		.pipe(dest('./css'))
		.pipe(gulpIf(serverEnabled, browserSync.stream()));
}

const scripts = () => {
	const jsFiles = [
		{ entry: 'bundle', path: './js/src/common/bundle.js' },
		{ entry: 'page-main', path: './js/src/pages/page-main.js' },
	];

	return src(jsFiles.map(item => item.path), { since: lastRun(scripts) })
		.pipe(remember('scripts'))
		.pipe(plumber({
			errorHandler: function (err) {
				console.log('scripts ', err.message);
				this.end();
			}
		}))
		.pipe(webpackStream({
			mode: MODE,
			entry: jsFiles.reduce((accumulator, currentValue) => {
				return Object.assign(accumulator, { [currentValue.entry]: currentValue.path })
			}, {}),
			output: {
				filename: '[name].js',
			},
			devtool: false,
			optimization: isProduction ? {
				minimize: true,
				minimizer: [
					new TerserPlugin({
						terserOptions: {
							warnings: false,
							compress: {
								comparisons: false,
							},
							parse: {},
							mangle: true,
							output: {
								comments: false,
								ascii_only: true,
							},
						},
						extractComments: false,
						sourceMap: false,
					}),
				],
				nodeEnv: MODE,
				sideEffects: true,
				concatenateModules: true,
			} : {},
			module: {
				rules: [
					{
						test: /\.(js)$/,
						exclude: /(node_modules)/,
						loader: 'babel-loader',
						query: {
							presets: ['@babel/preset-env']
						}
					}
				]
			},
		}))
		.pipe(gulpIf(isProduction, size({ showFiles: true, title: 'JS' })))
		.pipe(dest('./js/min'))
		.pipe(gulpIf(serverEnabled, browserSync.stream()));
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
							dest:'../../../scss/modules/_sprite.scss',
						}
					}
				}
			},
		}))
		.pipe(dest('./images/svg'));
};

const watchTask = () => {
	watch(['./js/src/common/*.js', './js/src/pages/*.js'], scripts);
	watch('./scss/**/*.scss', styles)
}

const defaultTask = serverEnabled ? parallel(watchTask, server) : watchTask;

task('default', defaultTask);
task('server', server);
task('sprite', createSvgSprite);
task('css', styles);
task('js', scripts);
task('build', parallel(styles, scripts));
