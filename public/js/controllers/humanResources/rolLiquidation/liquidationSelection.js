'use strict';
angular.module('app').controller('liquidationSelectionCtrl', [
  '$scope',
  '$modal',
  '$state',
  '$window',
  'server',
  function ($scope, $modal, server, EmployeSelectionsModal, $state, $window) {

    $scope.openPreLiquidarModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/employePreLiquidados.html',
        controller: 'LiquidationCtrl',
        size: 'lg',
        resolve: {
          EmployeSelectionsModal: function() //scope del modal
          {
            //console.log($scope.massiveBonus.department_id);
            return $scope.employeSelections;

          }
        }
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };

  }

]);
