const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');

gulp.task('default', function() {
     return gulp.src("./public/scss/**/*.scss" )
    .pipe(sass({
        includePaths: ["node_modules/bootstrap/scss","node_modules/toastr"],

        outputStyle: 'expanded',
        sourceComments: false
        })).on('error', sass.logError)
         .pipe(autoprefixer({
        browsers: ["last 2 versions"],
        cascade: false
    }))
     .pipe(gulp.dest("./public/css"))
});

// copiar jQuery
gulp.src('node_modules/jquery/dist/*.min.js')
    .pipe(gulp.dest('./public/js'));

// copiar javascript de bootstrap
gulp.src('node_modules/bootstrap/dist/js/*.min.js')
    .pipe(gulp.dest('./public/js'));

// copiar javascript de toastr
gulp.src('node_modules/toastr/toastr.js')
    .pipe(gulp.dest('./public/js'));

// copiar javascript de select2
gulp.src('node_modules/select2/dist/js/select2.min.js')
    .pipe(gulp.dest('./public/js'));

// copiar css de select2
gulp.src('node_modules/select2/dist/css/select2.min.css')
    .pipe(gulp.dest('./public/css'));

// watch para la tarea default -> iniciar con gulp
gulp.watch('./public/scss/**/*.scss',['default']);