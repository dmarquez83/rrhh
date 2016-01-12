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
              //console.log($scope.rolLiquidation.monthSettlement,'el mes');
              return $scope.rolLiquidation.monthSettlement;

            },
            SinceDate:function() //scope del modal
            {
              //console.log($scope.rolLiquidation.firstDay,'firstDay');
              return $scope.rolLiquidation.firstDay;

            },
            UntilDate:function() //scope del modal
            {
              //date:'yyyy-MM-dd'
              //console.log($scope.rolLiquidation.lastDay,'lastDay');
              return $scope.rolLiquidation.lastDay;

            },
            Status:function()
            {
              return '';
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

    $scope.openLiquidarModal = function (status) {
     // if($scope.employeSelections.length==0){
        var modalInstance = $modal.open({
          templateUrl: '../../views/humanResources/rolLiquidation/employeLiquidados.html',
          controller: 'LiquidationCtrl',
          size: 'lg',
          resolve: {
            EmployeSelectionsModal: function() //scope del modal
            {
             if($scope.paymenthroles.length>0)
                return $scope.paymenthroles;
             else
                return $scope.resumenpaymenthroles;
            },
            TypeSettlement:function() //scope del modal
            {
              //console.log($scope.typeSettlement,'el tipo');
              return $scope.typeSettlement;

            },
            MonthSettlement:function() //scope del modal
            {
              //console.log($scope.rolLiquidation.monthSettlement,'el mes');
              return $scope.rolLiquidation.monthSettlement;

            },
            SinceDate:function() //scope del modal
            {
              //console.log($scope.rolLiquidation.firstDay,'firstDay');
              return $scope.rolLiquidation.firstDay;

            },
            UntilDate:function() //scope del modal
            {
              //date:'yyyy-MM-dd'
              //console.log($scope.rolLiquidation.lastDay,'lastDay');
              return $scope.rolLiquidation.lastDay;

            },
            Status:function()
            {
              return status;
            }
          }
        });
        modalInstance.result.then(function () {
          $window.location.reload();
        });
    //  }

    };

  }

]);
