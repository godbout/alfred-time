var gulp = require('gulp');
var phpspec = require('gulp-phpspec');
var notify = require('gulp-notify');

gulp.task('test', function() {
    gulp.src('tests/spec/**/*.php')
        .pipe(phpspec('vendor/bin/phpspec', { clear: true, notify: true }))
        .on('error', notify.onError({
            title: 'Fuck',
            message: 'Dude. The tests failed! This is not good.',
            icon: __dirname + '/fail.png'
        }))
        .pipe(notify({
            title: 'Success',
            message: 'All tests have returned green beautifully!',
            icon: __dirname + '/success.png'
        }));
});

gulp.task('watch', function() {
    gulp.watch(['tests/spec/**/*.php', 'src/**/*.php'], ['test']);
});

gulp.task('default', ['test', 'watch']);

