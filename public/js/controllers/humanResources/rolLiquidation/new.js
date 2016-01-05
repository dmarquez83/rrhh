'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
  '$scope',
  '$modal',
  'server',
  '$window',
  '$rootScope',
  function ($scope,$modal,server, $window,$rootScope) {
      $scope.rolLiquidation = {};
      $scope.rolLiquidation.departmentName = '';
      $scope.departments = [];
      $scope.employeSelections = [];
      var modalInstance;

      $scope.$on('employees', function (event, values) {
          $scope.employeSelections = values.employeSelections;
      });

      server.getAll('departments').success(function (data) {
          $scope.departments = data;
      });

      $scope.openDptoEmployeModal = function () {
        var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/dptoEmployeSelections.html',
        controller: 'RolLiquidationCtrl',
        size: 'lg'
      });
        modalInstance.result.then(function () {
            $window.location.reload();
        });
      };

      server.post('getEmployees').success(function(result){
          $scope.employees = (result);
      });

      $scope.listEmployees = function(){
         server.post('getEmployees').success(function(result){
              if($scope.rolLiquidation.department_id){
                  $scope.employees = _(result).where({ 'department_id':  $scope.rolLiquidation.department_id });
              }
              else
              {
                  $scope.employees = result;
              }
         });
      };

      $scope.checkAll = function () {
          if ($scope.selectedAll) {
              $scope.selectedAll = true;
          } else {
              $scope.selectedAll = false;
          }
          angular.forEach($scope.employees, function (employe) {
              employe.Selected = $scope.selectedAll;
          });

      };

      $scope.saveEmploye = function(){
          var cuenta = 0;
          angular.forEach($scope.employees, function (employe) {
              if(employe.Selected){
                  $scope.employeSelections[cuenta] = employe;
                  cuenta++;
              }
          });
          $rootScope.$broadcast('employees', { employeSelections: $scope.employeSelections });
          $modalInstance.dismiss();
      };

      $scope.openPreLiquidarModal = function () {
        modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/employePreLiquidados.html',
        controller: 'RolLiquidationCtrl',
        size: 'xlg'
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


      $scope.cancel = function () {
          console.log('otherFunction');
          //DO SOME STUFF
          //....
          //THEN CLOSE MODAL HERE
          modalInstance.close();
      }


      handlePanelAction();
  }

]);
