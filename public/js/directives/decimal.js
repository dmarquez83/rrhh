'user strict';
angular.module('app').directive('decimal', function () {

  var NUMBER_REGEXP = /^\s*(\+)?(\d+|(\d*(\.(\d{1,2})?$)))\s*$/;

  function link(scope, element, attrs, model){
    
    var lastValidValue = 0;
    element.addClass('text-right');
    function formatViewValue(value) {
      return model.$isEmpty(value) ? '' : '' + value;
    }
    model.$parsers.push(function (value) {
      var empty = model.$isEmpty(value);
      if (empty || NUMBER_REGEXP.test(value)) {
        lastValidValue = value === '' ? null : empty ? value : value;
        if(value < scope.min && empty == false) {
          lastValidValue = value === '' ? null : empty ? value : value;
          model.$setViewValue(formatViewValue(lastValidValue));
          model.$render();
        }
        if(value > scope.max && empty == false){
          value = 100;
          lastValidValue = value === '' ? null : empty ? value : value;
          model.$setViewValue(formatViewValue(lastValidValue));
          model.$render();
        }
      } else {
        model.$setViewValue(formatViewValue(lastValidValue));
        model.$render();
      }
      model.$setValidity('text', true);
      return lastValidValue;
    });

  }

  return {
    restrict: 'A',
    require: 'ngModel',
    scope: {
      min: '=decimalMin',
      max: '=decimalMax'
    },
    link : link
  }
});
