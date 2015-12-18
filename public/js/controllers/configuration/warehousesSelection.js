'use strict';
angular.module('app').controller('WarehousesSelectionCtrl', [
  '$scope',
  '$modal',
  '$state',
  '$window',
  'server',
  function ($scope, $modal, $state, $window, server) {
    $scope.openWarehouses = function () {
      var modalInstance = $modal.open({
          templateUrl: '../../views/configuration/warehousesSelection.html',
          controller: 'WarehousesModalCtrl',
          size: 'lg',
          resolve: {
            currentWarehouseId: function () {
              return $scope.currentWarehouse._id;
            }
          }
        });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };
    function getCurrentWarehouse() {
      server.post('getCurrentWarehouse').success(function (data) {
        if (data) {
          $scope.currentWarehouse = data;
        }
      });
    }
    getCurrentWarehouse();
  }
]);