'use strict';
angular.module('app').controller('liquidationSelectionCtrl', [
  '$scope',
  '$modal',
  '$state',
  '$window',
  'server',
  function ($scope, $modal, server, EmployeSelectionsModal, TypeSettlement,MonthSettlement,SinceDate,UntilDate, $state, $window) {

    $scope.openPreLiquidarModal = function () {
      if($scope.employeSelections.length>0){
        var modalInstance = $modal.open({
          templateUrl: '../../views/humanResources/rolLiquidation/employePreLiquidados.html',
          controller: 'LiquidationCtrl',
          size: 'lg',
          resolve: {
            EmployeSelectionsModal: function() //scope del modal
            {
              return $scope.employeSelections;

            },
            TypeSettlement:function() //scope del modal
            {
              //console.log($scope.typeSettlement,'el tipo');
              return $scope.typeSettlement;

            },
            MonthSettlement:function() //scope del modal
            {
              console.log($scope.rolLiquidation.monthSettlement,'el mes');
              return $scope.rolLiquidation.monthSettlement;

            },
            SinceDate:function() //scope del modal
            {
              console.log($scope.sinceDate,'sinceDate');
              return $scope.sinceDate;

            },
            UntilDate:function() //scope del modal
            {
              console.log($scope.untilDate,'untilDate');
              return $scope.untilDate;

            }
          }
        });
        modalInstance.result.then(function () {
          $window.location.reload();
        });
      }else{
        alert('No hay empleados seleccionados');
      }

    };

  }

]);
