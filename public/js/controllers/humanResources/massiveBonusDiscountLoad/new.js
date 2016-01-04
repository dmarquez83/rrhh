'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  '$rootScope',
  function ($scope, documentValidate, server, $rootScope) {
    var typeBonus = '';
    var dateMassive = moment().format();
    $scope.massiveBonus = {};
    $scope.massiveBonus.typeBonus = [];
    $scope.massiveBonus.frequencyBonus = [];
    $scope.departments = [];
    $scope.bonusdiscounts = [];
    $scope.type = [];
    $scope.employeSelections = [];
    $scope.assignedDiscounts = {};
    $scope.assignedBonus = {};
    $scope.quantityEmploye = 0;


    $rootScope.$on('employees', function (event, values) {
        //console.log(values.employeSelections);
      $scope.employeSelections = values.employeSelections;
    });

    $scope.employeSelections =  $rootScope.employeSelections;

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });

    $scope.searchTypeBonus = function (index) {

      if($scope.massiveBonus.typeBonus[index] == 'bonus')
        typeBonus = 'bonus';

      if($scope.massiveBonus.typeBonus[index] == 'discounts')
        typeBonus = 'discounts';


      if(typeBonus){
        server.getAll(typeBonus).success(function (data) {
          $scope.type[index] = data;
        });

      }
    };

    $scope.valtype = function(index){

      //Object.keys($scope.massiveBonus.type).splice(1, 1);
      //$scope.massiveBonus.type.splice(1, 1);

      var quantity = (Object.keys($scope.massiveBonus.type).length)-1;
      var counter = 0;

      angular.forEach($scope.massiveBonus.type, function (type) {
        if(type._id == $scope.massiveBonus.type[index]._id && quantity != counter){
          toastr.error('Error', 'Este nombre ya se encuentra seleccione otro');
          $scope.massiveBonus.type[index] = {};
        }
        counter++;
      });
    };


    $scope.addBonusDiscount = function() {
      $scope.bonusdiscounts.push({ hecho: true });
    };

    $scope.deleteBonusDiscount = function(index){
      $scope.bonusdiscounts.splice(index, 1);
    }

    $scope.save = function(){

      if($scope.employeSelections){
        angular.forEach($scope.employeSelections, function (employee) {

          //console.log(employee);

          var index = 0;

          angular.forEach($scope.massiveBonus.typeBonus, function (type) {


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

          });

        });

      }else {
        toastr.warning('Debe seleccionar al Menos un Empleado');
      }

    };


    handlePanelAction();
  }
]);