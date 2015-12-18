var gulp = require('gulp');
var minifycss = require('gulp-minify-css');
var minifyjs = require('gulp-uglify');
var minifyhtml = require('gulp-minify-html');
var autoprefixer = require('gulp-autoprefixer');
var imagemin = require('gulp-imagemin');

var concat = require('gulp-concat');

var paths = {
    css: 'public/styles/*.css',
    js: 'public/js/**/*.js',
    images: 'public/images/**',
    views: 'public/views/**/*.html'
}
var dest = {
    css: 'public/dist/css/',
    js: 'public/dist/js/',
    images: 'public/dist/images/',
    views: 'public/dist/views'
}

var bowerCss = [
    'public/bower_components/jquery-ui/themes/base/theme.css',
    'public/bower_components/datatables/media/css/jquery.dataTables.css',
    'public/bower_components/datatables-tabletools/css/dataTables.tableTools.css',
    'public/bower_components/datatables-responsive/css/dataTables.responsive.css',
    'public/bower_components/datatables-scroller/css/dataTables.scroller.css',
    'public/bower_components/datatables-colvis/css/dataTables.colVis.css',
    'public/bower_components/angular-datatables/dist/plugins/bootstrap/datatables.bootstrap.css',
    'public/bower_components/selectize/dist/css/selectize.bootstrap3.css',
    'public/bower_components/AlertifyJS/build/css/alertify.css',
    'public/bower_components/AlertifyJS/build/css/themes/semantic.css',
    'public/bower_components/ngprogress/ngProgress.css',
    'public/bower_components/angular-chart.js/dist/angular-chart.css',
    'public/bower_components/ng-image-input-with-preview/dist/ng-image-input-with-preview.css',
    'public/bower_components/toastr/toastr.css',
    'public/bower_components/datatables-fixedheader/css/dataTables.fixedHeader.css',
    'public/bower_components/sweetalert/dist/sweetalert.css',
    'public/custom_components/bootstrap-wizard/css/bwizard.min.css',
    'public/bower_components/tether-shepherd/dist/css/shepherd-theme-default.css',
    'public/bower_components/ui-select/dist/select.css',
    'public/bower_components/offline/themes/offline-theme-chrome.css',
    'public/bower_components/offline/themes/offline-language-spanish.css',
    'public/bower_components/dropzone/dist/dropzone.css',
];

var themeCss = [
    'public/styles/theme/animate.css',
    'public/styles/theme/style.css',
    'public/styles/theme/style.responsive.css',
    'public/styles/theme/theme.default.css',
    'public/styles/style.css'
];

var bowerJs = [
    'public/bower_components/jquery/dist/jquery.js',
    'public/bower_components/bootstrap/dist/js/bootstrap.js',
    'public/bower_components/toastr/toastr.js',
    'public/bower_components/moment/moment.js',
    'public/bower_components/moment/locale/es.js',
    'public/bower_components/underscore/underscore.js',
    'public/bower_components/AlertifyJS/build/alertify.js',
    'public/bower_components/selectize/dist/js/standalone/selectize.js',
    'public/custom_components/datatables/media/js/jquery.dataTables.js',
    'public/bower_components/datatables-buttons/js/dataTables.buttons.js',
    'public/bower_components/datatables-buttons/js/buttons.bootstrap.js',
    'public/bower_components/datatables-buttons/js/buttons.print.js',
    'public/bower_components/datatables-buttons/js/buttons.colVis.js',
    'public/bower_components/Chart.js/Chart.js',
    'public/bower_components/js-xlsx/dist/jszip.js',
    'public/bower_components/js-xls/dist/xls.js',
    'public/bower_components/js-xlsx/dist/xlsx.js',
    'public/bower_components/big.js/big.js',
    'public/bower_components/slimScroll/jquery.slimscroll.js',
    'public/bower_components/sweetalert/dist/sweetalert.min.js',
    'public/bower_components/mathjs/dist/math.js',
    'public/bower_components/jquery.steps/build/jquery.steps.js',
    'public/bower_components/clipboard/dist/clipboard.js',
    'public/bower_components/tether-shepherd/dist/js/tether.js',
    'public/bower_components/tether-shepherd/dist/js/shepherd.js',
    'public/bower_components/downloadjs/download.js',
    'public/custom_components/abdmob/x2js/xml2json.js',
    'public/bower_components/offline/offline.js',
    'public/bower_components/jQuery.print/jQuery.print.js',
    'public/bower_components/angular/angular.js',
    'public/bower_components/angular-sanitize/angular-sanitize.js',
    'public/bower_components/angular-i18n/angular-locale_es-ec.js',
    'public/bower_components/angular-ui-router/release/angular-ui-router.js',
    'public/bower_components/angular-animate/angular-animate.js',
    'public/bower_components/angular-sanitize/angular-sanitize.js',
    'public/bower_components/angular-touch/angular-touch.js',
    'public/bower_components/angular-bootstrap/ui-bootstrap.js',
    'public/bower_components/angular-bootstrap/ui-bootstrap-tpls.js',
    'public/bower_components/angular-ui-utils/ui-utils.js',
    'public/bower_components/ngprogress/build/ngProgress.js',
    'public/custom_components/angular-datatables/dist/angular-datatables.js',
    'public/bower_components/angular-selectize2/dist/angular-selectize.js',
    'public/bower_components/ng-table/dist/ng-table.js',
    'public/bower_components/ng-image-input-with-preview/dist/ng-image-input-with-preview.js',
    'public/bower_components/angular-chart.js/dist/angular-chart.js',
    'public/bower_components/pdfmake/build/pdfmake.js',
    'public/bower_components/pdfmake/build/vfs_fonts.js',
    'public/bower_components/jsbarcode/JsBarcode.js',
    'public/bower_components/jsbarcode/CODE128.js',
    'public/bower_components/angular-bootstrap-checkbox/angular-bootstrap-checkbox.js',
    'public/bower_components/angular-sweetalert/SweetAlert.js',
    'public/custom_components/angular-input-masks/angular-input-masks-standalone.js',
    'public/bower_components/angular-xlsx/dist/angular-xlsx.js',
    'public/bower_components/angular-drag-and-drop-lists/angular-drag-and-drop-lists.js',
    'public/custom_components/bootstrap-wizard/js/bwizard.js',
    'public/bower_components/angular-base64/angular-base64.js',
    'public/bower_components/ui-select/dist/select.js'
];

gulp.task('themeCss', function() {
    return gulp.src(themeCss)
        .pipe(autoprefixer('last 15 version'))
        .pipe(minifycss())
        .pipe(concat('theme.min.css'))
        .pipe(gulp.dest(dest.css));
});

gulp.task('componentsCss', function() {
    return gulp.src(bowerCss)
        .pipe(autoprefixer('last 15 version'))
        .pipe(minifycss())
        .pipe(concat('components.min.css'))
        .pipe(gulp.dest(dest.css));
});

gulp.task('componentsJs-production', function() {
    return gulp.src(bowerJs)
        .pipe(minifyjs())
        .pipe(concat('components.min.js'))
        .pipe(gulp.dest(dest.js));
});

gulp.task('componentsJs', function() {
    return gulp.src(bowerJs)
        .pipe(concat('components.min.js'))
        .pipe(gulp.dest(dest.js));
});

gulp.task('css', function() {
    return gulp.src(paths.css)
        .pipe(autoprefixer('last 15 version'))
        .pipe(minifycss())
        .pipe(concat('main.min.css'))
        .pipe(gulp.dest(dest.css));
});


gulp.task('js-production', function() {
    return gulp.src(paths.js)
        .pipe(minifyjs())
        .pipe(concat('main.min.js'))
        .pipe(gulp.dest(dest.js));
});

gulp.task('js', function() {
    return gulp.src(paths.js)
        .pipe(concat('main.min.js'))
        .pipe(gulp.dest(dest.js));
});

gulp.task('views', function() {
    return gulp.src(paths.views)
        .pipe(minifyhtml({
            empty: true,
            cdata: true,
            spare: true
        }))
        .pipe(gulp.dest(dest.views));
});

gulp.task('compress-img', function () {
  return gulp.src(paths.images)
    .pipe(imagemin({
        progressive: true,
        svgoPlugins: [{removeViewBox: false}]
    }))
    .pipe(gulp.dest(dest.images));
});

gulp.task('watch', function() {
    gulp.watch(paths.css, ['css']);
    gulp.watch(paths.js, ['js']);
    gulp.watch(paths.views, ['views']);
});

gulp.task('default', ['themeCss', 'componentsCss','componentsJs', 'css', 'js', 'views', 'compress-img', 'watch']);

gulp.task('production', ['themeCss', 'componentsCss','componentsJs-production', 'css', 'js-production', 'views', 'compress-img']);

