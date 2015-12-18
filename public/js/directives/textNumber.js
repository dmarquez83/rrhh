'use strict';
angular.module('app').directive('textNumber', function () {
  var NUMBER_REGEXP = /^[a-zA-Z0-9_áéíóúñ\s]*$/;
  function link(scope, el, attrs, ngModelCtrl) {
    var lastValidValue;
    function formatViewValue(value) {
      return ngModelCtrl.$isEmpty(value) ? '' : '' + value;
    }
    ngModelCtrl.$parsers.push(function (value) {
      var empty = ngModelCtrl.$isEmpty(value);
      if (empty || NUMBER_REGEXP.test(value)) {
        lastValidValue = value === '' ? null : empty ? value : value;
      } else {
        ngModelCtrl.$setViewValue(formatViewValue(lastValidValue));
        ngModelCtrl.$render();
      }
      ngModelCtrl.$setValidity('text', true);
      return lastValidValue;
    });
  }
  return {
    restrict: 'A',
    require: 'ngModel',
    link: link
  };
});