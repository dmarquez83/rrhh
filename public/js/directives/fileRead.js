'user strict';
angular.module('app').directive('fileReader', function($q) {
  var slice = Array.prototype.slice;

  return {
    restrict: 'A',
    require: '?ngModel',
    link: function(scope, element, attrs, ngModel) {
      if (!ngModel) return;

      ngModel.$render = function() {};

      element.bind('change', function(e) {
        var element = e.target;

        $q.all(slice.call(element.files, 0).map(readFile))
          .then(function(values) {
            if (element.multiple) ngModel.$setViewValue(values);
            else ngModel.$setViewValue(values.length ? values[0] : null);
          });

        function readFile(file) {
          var fileSizeKb = file.size / 1024;

          if(fileSizeKb <= attrs.maxFileSize){
            var deferred = $q.defer();

            var reader = new FileReader();
            reader.onload = function(e) {
              deferred.resolve(e.target.result);
            };
            reader.onerror = function(e) {
              deferred.reject(e);
            };
            reader.readAsDataURL(file);

            return deferred.promise;
          } else {
            element.value = null;
            toastr.warning("El tamaño máximo para el archivo es 250KB");
          }
          return null;
        }

      });

    }
  };
});
