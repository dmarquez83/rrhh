'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
  '$scope',
  '$modal',
  'server',
  function ($scope,$modal,server) {
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
        modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/rolLiquidation/dptoEmployeSelections.html',
        controller: 'RolLiquidationCtrl',
        size: 'lg',
        resolve: {
          Id_Depart: function() //scope del modal
          {
            //console.log($scope.rolLiquidation.department_id);
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
              if($scope.rolLiquidation.department_id){
                  $scope.employees = _(result).where({ 'department_id':  $scope.rolLiquidation.department_id });
                 // $scope.rolLiquidation.departmentName = _($scope.departments).findWhere({ '_id': $scope.rolLiquidation.department_id }).name;
              }
              else
              {
                  $scope.employees = result;
                 // $scope.rolLiquidation.departmentName = _($scope.departments).findWhere({ '_id': $scope.employees.department_id }).name;
              }

         })
      }

      listEmployees();


      $scope.saveEmploye = function(){

          if($scope.employeSelections){
              angular.forEach($scope.employeSelections, function (employee) {

                  //console.log(employee);

                  var index = 0;

                  /*angular.forEach($scope.massiveBonus.typeBonus, function (type) {


                      if(type == 'discounts'){
                          employee.discounts = _(employee).has('discounts') ? employee.discounts : [];
                          $scope.assignedDiscounts = angular.copy($scope.massiveBonus.type[index]);
                          //$scope.assignedDiscounts.date = moment().format();
                          $scope.assignedDiscounts.frequency = $scope.massiveBonus.frequencyBonus[index];
                          employee.discounts.push($scope.assignedDiscounts);
                          //employee.discounts.push($scope.massiveBonus.frequencyBonus[index]);
                          var discounts = { 'discounts': angular.copy(employee.discounts) };
                          //console.log(discounts);
                          server.update('employee', discounts, employee._id).success(function (data) {
                              toastr[data.type](data.msg);
                          });
                      }

                      if(type == 'bonus'){
                          employee.bonus = _(employee).has('bonus') ? employee.bonus : [];
                          $scope.assignedBonus = angular.copy($scope.massiveBonus.type[index]);
                          //$scope.assignedBonus.date = moment().format();
                          $scope.assignedBonus.frequency = $scope.massiveBonus.frequencyBonus[index];
                          employee.bonus.push($scope.assignedBonus);
                          //employee.bonus.push($scope.massiveBonus.frequencyBonus[index]);
                          var bonus = { 'bonus': angular.copy(employee.bonus) };
                          //console.log(bonus);
                          server.update('employee', bonus, employee._id).success(function (data) {
                              toastr[data.type](data.msg);
                          });

                      }

                      index++;

                  });*/

              });

          }else {
              toastr.warning('Debe seleccionar al Menos un Empleado');
          }

      };


      $scope.openPreLiquidarModal = function () {
        modalInstance = $modal.open({
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


    $scope.cancel = function () {
          modalInstance.close();

    };

      /*var ModalInstanceCtrl = function ($scope,$modalInstance) {

          $scope.ok = function(){
              $modalInstance.close();
          }

          $scope.cancel = function(){
              $modalInstance.dismiss('cancel');
          }
      };*/


    handlePanelAction();
  }


]);
