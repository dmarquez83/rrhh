'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
  '$scope',
  '$modal',
  'server',
  '$state',
  '$window',
  '$rootScope',
  function ($scope,$modal,server,$state, $window,$rootScope) {
      $scope.rolLiquidation = {};
      $scope.rolLiquidation.departmentName = '';
      $scope.departments = [];
      $scope.employees = [];
      $scope.employeSelections = [];
      $scope.countEmployee = 0;


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
          $scope.countEmployee = $scope.employees.length;
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
              $scope.countEmployee = $scope.employees.length;
          } else {
              $scope.selectedAll = false;
              $scope.countEmployee = 0;
          }
          angular.forEach($scope.employees, function (employe) {
              employe.Selected = $scope.selectedAll;
          });

      };

      $scope.countCheck = function(){
          var cuenta = 0;
          angular.forEach($scope.employees, function (employe) {
              if(employe.Selected){
                  cuenta++;
              }
          });
          $scope.countEmployee = cuenta;
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

         // $modalInstance.dismiss();
      };

      $scope.searchEmployeAct = function () {
          server.post('getEmployees').success(function(result){
              $scope.employees = _(result).where({ 'status':  'Activo' });
          });
          $rootScope.$broadcast('employees', { employeSelections: $scope.employees });

      };

      $rootScope.$on('employees', function (event, values) {
          $scope.employeSelections = values.employeSelections;
      });


    $scope.searchSettlement = function (fecha) {

      //buscar si hay liquidaciones en el mes/quincena seleccionada y arrojar mensaje si ya fue hecha
    };

      $scope.listFechas= function(){
          //alert('Tipo ' + $scope.typeSettlement);
          $scope.rolLiquidation.firstDay='';
          $scope.rolLiquidation.lastDay='';

          $scope.date = new Date();
          $scope.anhoAct = $scope.date.getFullYear();

          if($scope.typeSettlement=='monthly'){
              $scope.mesSel = $scope.rolLiquidation.monthSettlement;

              //alert('Año ' + $scope.anhoAct + ' ,  Mes' + $scope.rolLiquidation.monthSettlement);

              $scope.rolLiquidation.firstDay =  new Date($scope.anhoAct, $scope.mesSel - 1, 1);
              $scope.rolLiquidation.lastDay = new Date($scope.anhoAct,$scope.mesSel, 0);
          }

          //console.log('Debes imprimir', $scope.rolLiquidation.firstDay, $scope.rolLiquidation.lastDay);


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
