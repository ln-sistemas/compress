var gulp = require('gulp');
var htmlmin = require('gulp-htmlmin');
 
gulp.task('html', function(){
  return gulp.src('./dev/index.php')
      .pipe(htmlmin({
        collapseWhitespace: true,
        ignoreCustomFragments: [ /<%[\s\S]*?%>/, /<\?[=|php]?[\s\S]*?\?>/ ]
      }))
      .pipe(gulp.dest('./'));
});