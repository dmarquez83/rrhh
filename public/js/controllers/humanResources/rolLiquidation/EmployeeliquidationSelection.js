'use strict';
angular.module('app').controller('EmployeeLiquidationSelection', [
  '$scope',
  '$modal',
  'server',
  '$state',
  '$window',
  function ($scope,$modal,server,$state, $window) {


    $scope.openDptoEmployeModal = function () {

      if($scope.rolLiquidation.monthSettlement){
        var modalInstance = $modal.open({
          templateUrl: '../../views/humanResources/rolLiquidation/dptoEmployeSelections.html',
          controller: 'EmployeeRollLiquidation',
          size: 'lg'
        });
        modalInstance.result.then(function () {
          $window.location.reload();
        });
      }else
      {
        toastr.error('Seleccione el Mes de Liquidacion para poder seleccionar los empleados');
      }

    };

  }

]);
