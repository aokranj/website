var gulp = require('gulp'); 
var sass = require('gulp-sass');
var livereload = require('gulp-livereload');

gulp.task('plugin', function () {
    gulp.src('public/wp-content/plugins/aokranj/sass/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/wp-content/plugins/aokranj/css'))
        .pipe(livereload());
});

gulp.task('theme', function () {
    gulp.src('public/wp-content/themes/aokranj/style.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/wp-content/themes/aokranj'))
        .pipe(livereload());
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch('public/wp-content/plugins/aokranj/sass/*.scss', ['plugin']);
    gulp.watch('public/wp-content/themes/aokranj/style.scss', ['theme']);
});

gulp.task('default', ['watch']);
