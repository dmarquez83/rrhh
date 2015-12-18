'user strict';
angular.module('app').directive('selectizeEmployee', function() {
  return {
    restrict: 'EA',
    require: '^ngModel',
    scope: {
      ngModel: '=',
      ngRequired: '@',
      id: '@',
      name: '@',
      tabindex: '@',
      employeesData: '=',
      ngDisabled: '='
    },
    templateUrl: '../../views/humanResources/employees/selectize.html',
    controller: [
      '$scope', 'server', 'transferData', '$timeout',
      function($scope, server, transferData, $timeout) {

        $scope.allEmployees = {};

        var getEmployees = function () {
          if ($scope.employeesData != '' && $scope.employeesData != undefined != $scope.employeesData != []){
            $scope.allEmployees.employees = $scope.employeesData;
          } else {
            server.getAll('employee').success(function (data) {
              $scope.allEmployees.employees = data;
              transferData.data.employees = data;
            });
          }
        };

        $scope.configSelectedEmployees = {
          create: false,
          valueField: '_id',
          labelField: 'names',
          render: {
            item: function (item, escape) {
              return '<div>' + item.names + ' ' + item.surnames + '</div>';
            },
            option: function (item, escape) {
              return '<div>' +
                '<p>' +
                '<span><strong>' + escape(item.names) + ' '+ escape(item.surnames) + '</strong></span><br/>' +
                '<span>' + escape(item.identification) + '</span>' +
                '</p>' +
                '</div>';

            }
          },
          searchField: [
            'identification',
            'names',
            'surnames'
          ],
          placeholder: 'Seleccione un empleado',
          maxItems: 1
        };

        $timeout(function () {
          getEmployees();
        }, 100);

      }],
    link: function(scope, iElement, iAttrs, ctrl) {
    }
  }
});
