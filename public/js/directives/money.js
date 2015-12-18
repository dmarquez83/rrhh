'use strict';
angular.module('app').directive('money', [
  '$filter',
  '$parse',
  function ($filter, $parse) {
    return {
      require: 'ngModel',
      link: function (scope, element, attrs, ngModelController) {
        var NUMBER_REGEXP = /^\s*(\+)?(\d+|(\d*(\.(\d{1,2})?$)))\s*$/;
        var lastValidValue;
        element.addClass('text-right');
        var decimals = $parse(attrs.decimals)(scope);
        function formatViewValue(value) {
          return ngModelController.$isEmpty(value) ? '' : '' + value;
        }
        ngModelController.$parsers.push(function (value) {
          var empty = ngModelController.$isEmpty(value);
          if (empty || NUMBER_REGEXP.test(value)) {
            lastValidValue = value === '' ? null : empty ? value : value;
          } else {
            ngModelController.$setViewValue(formatViewValue(lastValidValue));
            ngModelController.$render();
          }
          return lastValidValue;
        });
        ngModelController.$formatters.push(function (value) {
          //convert data from model format to view format
          var empty = ngModelController.$isEmpty(value);
          if (empty || NUMBER_REGEXP.test(value)) {
            lastValidValue = value === '' ? null : empty ? value : value;
          } else {
            ngModelController.$setViewValue(formatViewValue(lastValidValue));
            ngModelController.$render();
          }
          return $filter('number')(formatViewValue(lastValidValue), decimals);  //converted
        });
        element.bind('focus', function () {
          element.val(ngModelController.$modelValue);
        });
        element.bind('blur', function () {
          // Apply formatting on the stored model value for display
          var formatted = $filter('number')(ngModelController.$modelValue, decimals);
          element.val(formatted);
        });
      }
    };
  }
]);