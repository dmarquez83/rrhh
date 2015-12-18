'use strict';
angular.module('app').directive('number', function () {

  var NUMBER_REGEXP = /^[0-9]*$/;

  function link(scope, el, attrs, ngModelCtrl) {
    var lastValidValue;
    el.addClass('text-right');
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