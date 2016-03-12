var gulp = require('gulp'); 
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');
var livereload = require('gulp-livereload');
var autoprefixer = require('gulp-autoprefixer');

function errorHandler(error) {
    console.log('==> ERROR:', error);
}

gulp.task('plugin', function () {
    gulp.src('public/wp-content/plugins/aokranj/sass/*.scss')
        .pipe(plumber({ errorHandler: errorHandler }))
        .pipe(sass())
        .pipe(autoprefixer({ browsers: ['last 2 versions'] }))
        .pipe(gulp.dest('public/wp-content/plugins/aokranj/css'))
        .pipe(livereload());
});

gulp.task('theme', function () {
    gulp.src('public/wp-content/themes/aokranj/style.scss')
        .pipe(plumber({ errorHandler: errorHandler }))
        .pipe(sass())
        .pipe(autoprefixer({ browsers: ['last 2 versions'] }))
        .pipe(gulp.dest('public/wp-content/themes/aokranj'))
        .pipe(livereload());
});

gulp.task('editor', function () {
    gulp.src('public/wp-content/themes/aokranj/editor.scss')
        .pipe(plumber({ errorHandler: errorHandler }))
        .pipe(sass())
        .pipe(autoprefixer({ browsers: ['last 2 versions'] }))
        .pipe(gulp.dest('public/wp-content/themes/aokranj'))
        .pipe(livereload());
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch('public/wp-content/plugins/aokranj/sass/*.scss', ['plugin']);
    gulp.watch('public/wp-content/themes/aokranj/style.scss', ['theme']);
    gulp.watch('public/wp-content/themes/aokranj/scss/*.scss', ['theme']);
    gulp.watch('public/wp-content/themes/aokranj/editor.scss', ['editor']);
});

gulp.task('default', ['watch']);
