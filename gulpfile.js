const gulp = require('gulp'),
    autoprefixer = require('autoprefixer'),
    composer = require('gulp-uglify/composer'),
    concat = require('gulp-concat'),
    cssnano = require('cssnano'),
    footer = require('gulp-footer'),
    format = require('date-format'),
    header = require('@fomantic/gulp-header'),
    order = require('ordered-read-streams'),
    postcss = require('gulp-postcss'),
    rename = require('gulp-rename'),
    replace = require('gulp-replace'),
    sass = require('gulp-sass')(require('sass')),
    uglifyjs = require('uglify-js'),
    uglify = composer(uglifyjs, console),
    pkg = require('./_build/config.json');

const banner = '/*!\n' +
    ' * <%= pkg.name %> - <%= pkg.description %>\n' +
    ' * Version: <%= pkg.version %>\n' +
    ' * Build date: ' + format("yyyy-MM-dd", new Date()) + '\n' +
    ' */';
const year = new Date().getFullYear();

let phpversion;
let modxversion;
pkg.dependencies.forEach(function (dependency, index) {
    switch (pkg.dependencies[index].name) {
        case 'php':
            phpversion = pkg.dependencies[index].version.replace(/>=/, '');
            break;
        case 'modx':
            modxversion = pkg.dependencies[index].version.replace(/>=/, '');
            break;
    }
});

const scriptsWeb = function () {
    return order([
        gulp.src('node_modules/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js'),
        gulp.src('node_modules/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js'),
        gulp.src('node_modules/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js'),
        gulp.src('node_modules/filepond/dist/filepond.js'),
    ])
        .pipe(concat('ajaxupload.min.js'))
        .pipe(uglify())
        .pipe(header(banner + '\n', {pkg: pkg}))
        .pipe(gulp.dest('assets/components/ajaxupload/js/web/'))
};
gulp.task('scripts', gulp.series(scriptsWeb));

const sassWeb = function () {
    return gulp.src([
        'source/sass/web/ajaxupload.scss'
    ])
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([
            autoprefixer()
        ]))
        .pipe(gulp.dest('source/css/web/'))
        .pipe(concat('ajaxupload.css'))
        .pipe(postcss([
            cssnano({
                preset: ['default', {
                    discardComments: {
                        removeAll: true
                    }
                }]
            })
        ]))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(footer('\n' + banner, {pkg: pkg}))
        .pipe(gulp.dest('assets/components/ajaxupload/css/web/'))
};
gulp.task('sass', gulp.series(sassWeb));

const lexiconWeb = function () {
    return gulp.src('node_modules/filepond/locale/*.js', {encoding: false})
        .pipe(replace('export default {\n', '<?php\n' +
            '/**\n' +
            ' * Web Lexicon Entries for AjaxUpload\n' +
            ' *\n' +
            ' * @package ajaxupload\n' +
            ' * @subpackage lexicon\n' +
            ' */\n'))
        .pipe(replace(/'/g, '’'))
        .pipe(replace(/ *(.*?):\s+([’`"])(.*?)\2,?\s*\n/g, '$$_lang[\'ajaxupload.$1\'] = \'$3\';\n'))
        .pipe(replace(/ *};?\n?$/, ''))
        .pipe(rename(function (path) {
            // Returns a completely new object, make sure you return all keys needed!
            return {
                dirname: path.basename.replace(/(.*)[-_].*/, '$1'),
                basename: 'web.inc',
                extname: '.php'
            };
        }))
        .pipe(gulp.dest('core/components/ajaxupload/lexicon/'));
};

gulp.task('lexicon', gulp.series(lexiconWeb));

const bumpCopyright = function () {
    return gulp.src([
        'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php',
        'core/components/ajaxupload/src/AjaxUpload.php',
    ], {base: './'})
        .pipe(replace(/Copyright 2013(-\d{4})? by/g, 'Copyright ' + (year > 2013 ? '2013-' : '') + year + ' by'))
        .pipe(gulp.dest('.'));
};
const bumpVersion = function () {
    return gulp.src([
        'core/components/ajaxupload/src/AjaxUpload.php',
    ], {base: './'})
        .pipe(replace(/version = '\d+\.\d+\.\d+-?[0-9a-z]*'/ig, 'version = \'' + pkg.version + '\''))
        .pipe(gulp.dest('.'));
};
const bumpDocs = function () {
    return gulp.src([
        'mkdocs.yml',
    ], {base: './'})
        .pipe(replace(/&copy; 2013(-\d{4})?/g, '&copy; ' + (year > 2013 ? '2013-' : '') + year))
        .pipe(gulp.dest('.'));
};
const bumpRequirements = function () {
    return gulp.src([
        'docs/index.md',
    ], {base: './'})
        .pipe(replace(/[*-] MODX Revolution \d.\d.*/g, '* MODX Revolution ' + modxversion + '+'))
        .pipe(replace(/[*-] PHP (v)?\d.\d.*/g, '* PHP ' + phpversion + '+'))
        .pipe(gulp.dest('.'));
};
const bumpComposer = function () {
    return gulp.src([
        'core/components/ajaxupload/composer.json',
    ], {base: './'})
        .pipe(replace(/"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig, '"version": "' + pkg.version + '"'))
        .pipe(gulp.dest('.'));
};
gulp.task('bump', gulp.series(bumpCopyright, bumpVersion, bumpDocs, bumpRequirements, bumpComposer));

gulp.task('watch', function () {
    // Watch .js files
    gulp.watch(['./source/js/**/*.js'], gulp.series('scripts'));
    // Watch .scss files
    gulp.watch(['./source/sass/**/*.scss'], gulp.series('sass'));
});

// Default Task
gulp.task('default', gulp.series('bump', 'scripts', 'sass'));
