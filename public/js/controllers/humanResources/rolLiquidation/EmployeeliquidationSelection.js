'use strict';
angular.module('app').controller('EmployeeLiquidationSelection', [
  '$scope',
  '$modal',
  'server',
  '$state',
  '$window',
  function ($scope,$modal,server,$state, $window) {

    $scope.openDptoEmployeModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/dptoEmployeSelections.html',
        controller: 'EmployeeRollLiquidation',
        size: 'lg'
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };

  }

]);
