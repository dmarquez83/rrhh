'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
  '$scope',
  '$modal',
  'server',
  function ($scope,$modal,server) {
      $scope.rolLiquidation = {};
      $scope.departments = [];
      $scope.employeSelections = [];

      $scope.$on('employees', function (event, values) {
          $scope.employeSelections = values.employeSelections;
      });

      $scope.employeSelections =  $scope.employeSelections;

      server.getAll('departments').success(function (data) {
          $scope.departments = data;
      });

      $scope.openDptoEmployeModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/dptoEmployeSelections.html',
        controller: 'RolLiquidationCtrl',
        size: 'lg',
        resolve: {
          Id_Depart: function() //scope del modal
          {
            console.log($scope.rolLiquidation.department_id);
            return $scope.rolLiquidation.department_id;
          }
        }
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };


      var listEmployees = function(){
         server.post('getEmployees').success(function(result){

              if($scope.rolLiquidation.department_id)
                  $scope.employees = _(result).where({ 'department_id':  $scope.rolLiquidation.department_id });
              else
                  $scope.employees = result;
          })
      }

      listEmployees();



    $scope.openPreLiquidarModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/employePreLiquidados.html',
        controller: 'RolLiquidationCtrl',
        size: 'xlg',
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

    $scope.searchEmployeAct = function () {

          //buscar todos los empleados existentes y con estatus activo
    };



    handlePanelAction();
  }


]);
