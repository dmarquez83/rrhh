'user strict';
angular.module('app').directive('inputDate', function($compile) {
  return {
    restrict: 'EA',
    require: '^ngModel',
    scope: {
      ngModel: '=',
      ngRequired: '@',
      ngChange: '=',
      dateId: '@',
      dateName: '@',
      tabindex: '@',
      ngDisabled: '=',
      minDate:'@',
      minDateUpdate:'=',
    },
    template: ''
       + '<input type="text" class="form-control" ng-model="ngModel" is-open="isDatePickerOpen" ng-required="ngRequired" '
       + ' min-date="today" close-text="Cerrar" datepicker-popup="yyyy-MM-dd" ng-click="open($event)" id="{{dateId}}" name="{{dateName}}" show-button-bar="false"'
       + ' tabindex="tabindex" ng-disabled="ngDisabled" '
       + '  />'
       + ' <span class="input-group-addon"><i class="fa fa-calendar"></i></span>',
    controller: [
    '$scope', 'server', 'transferData', '$timeout',
    function($scope) {
      if ($scope.minDate === 'today'){
        $scope.today = new Date();
      }

      if ($scope.minDateUpdate) {
        $scope.today = moment($scope.minDateUpdate);
      }

      $scope.$watch('minDateUpdate', function(newValue){
        if (newValue) {
          $scope.today = moment($scope.minDateUpdate);
          try {$scope.$digest()} catch(error) {};
        }
      });

      $scope.isDatePickerOpen = false;
      $scope.open = function() {
         $scope.isDatePickerOpen = true;
       };

    }]
  };
});
