'use strict';
angular.module('app').controller('RolPagoEmployeeSelection', [
  '$scope',
  '$modal',
  'server',
  '$state',
  '$window',
  function ($scope,$modal,server,$state, $window) {


    $scope.openRolPagoEmployeModal = function (employee) {
        var modalInstance = $modal.open({
          templateUrl: '../../views/humanResources/rolLiquidation/detailRolPago.html',
          controller: 'RolPagoEmployeeCtrl',
          size: 'sm',
          resolve: {
              EmployeSelectionsModal: function () //scope del modal
              {
                  return employee;

              },
              typeSettlement: function () //scope del modal
              {
                  return $scope.typeSettlement;

              },
              monthSettlement: function () //scope del modal
              {
                  return $scope.monthSettlement;

              }
          }
        });
        modalInstance.result.then(function () {
          $window.location.reload();
        });
    };

  }

]);
