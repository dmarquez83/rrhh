'use strict';
angular.module('app').controller('WarehousesModalCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'currentWarehouseId',
  function ($scope, $modalInstance, server, currentWarehouseId) {
    $scope.selectedWarehouse = undefined;
    $scope.warehouses = [];
    $scope.indexWarehouse = 0;
    getWarehouses();

    $scope.selectWarehouse = function (index) {
      $scope.indexWarehouse = index;
      $scope.selectedWarehouse = $scope.warehouses[index];
    };

    $scope.ok = function () {
      server.post('setSelectedWarehouse', { warehouse: $scope.selectedWarehouse }).success(function (data) {
        $modalInstance.close();
      });
    };

    $scope.cancel = function () {
      $modalInstance.dismiss();
    };

    function getWarehouses() {
      server.post('getWarehouses').success(function (data) {
        if (data) {
          $scope.warehouses = data;
          _($scope.warehouses).each(function (war, ind, wars) {
            if (war._id === currentWarehouseId) {
              $scope.indexWarehouse = ind;
            }
          });
        }
      });
    }

  }
]);