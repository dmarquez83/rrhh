'user strict';
angular.module('app').directive('selectizeDriver', function() {
  return {
    restrict: 'EA',
    require: '^ngModel',
    scope: {
      ngModel: '=',
      ngRequired: '@',
      id: '@',
      name: '@',
      tabindex: '@',
      driversData: '=',
      ngDisabled: '='
    },
    templateUrl: '../../views/logistics/selectizeDriver.html',
    controller: [
      '$scope', 'server', 'transferData', '$timeout',
      function($scope, server, transferData, $timeout) {

        $scope.allDrivers = {};


        server.getAll('drivers').success(function (data) {
          $scope.allDrivers.drivers = data;
          transferData.data.drivers = data;
        });


        $scope.configSelectedDrivers = {
          create: false,
          valueField: '_id',
          labelField: 'name',
          render: {
            item: function (item, escape) {
              return '<div>' + item.name + '</div>';
            },
            option: function (item, escape) {
              return '<div>' + '<h6>' + item.name  + '<small>' + escape(item.identification) + '</small>' + '</h6>' + '</div>';
            }
          },
          searchField: [
            'identification',
            'name'
          ],
          placeholder: 'Seleccione un transportista',
          maxItems: 1
        };



      }],
    link: function(scope, iElement, iAttrs, ctrl) {
    }
  }
});
