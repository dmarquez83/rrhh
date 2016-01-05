'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
  '$scope',
  '$modal',
  'server',
  function ($scope,$modal,server) {

    $scope.openDptoEmployeModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/dptoEmployeSelections.html',
        controller: 'RolLiquidationCtrl',
        size: 'lg',
        resolve: {
          Id_Depart: function() //scope del modal
          {
            //console.log($scope.massiveBonus.department_id);
            return $scope.department_id;

          }
        }
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };


    $scope.openPreLiquidarModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/employePreLiquidados.html',
        controller: 'RolLiquidationCtrl',
        size: 'lg',
        resolve: {
          Id_Depart: function() //scope del modal
          {
            //console.log($scope.massiveBonus.department_id);
            return $scope.department_id;

          }
        }
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };

    $scope.searchSettlement = function (fecha) {

      //buscar si hay liquidaciones en el mes/quincena seleccionada y arrojar mensaje si ya fue hecha
    };


    handlePanelAction();
  }


]);
