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
          var index = 0;

          angular.forEach($scope.massiveBonus.typeBonus, function (type) {
            if(type == 'discounts'){
              var existe = false;
              angular.forEach(employee.discounts, function (employeeDiscounts) {
                if(employeeDiscounts.discount.code == $scope.massiveBonus.type[index].code){
                  existe = true;
                }
              });
              if(existe == false){
                employee.discounts = _(employee).has('discounts') ? employee.discounts : [];
                $scope.assignedDiscounts = {'discount': angular.copy($scope.massiveBonus.type[index])};
                $scope.assignedDiscounts.date = moment().format();
                $scope.assignedDiscounts.frequency = $scope.massiveBonus.frequencyBonus[index];
                employee.discounts.push($scope.assignedDiscounts);
                var discounts = { 'discounts': angular.copy(employee.discounts) };
                server.update('employee', discounts, employee._id).success(function (data) {
                  toastr[data.type](data.msg);
                });
              }
            }
            if(type == 'bonus'){
              /*$scope.searchBonus = _.map(
                  _.where(employee.bonus, {code : $scope.massiveBonus.type[index].code}),
                  function(person) {
                    return { Name: person.name};
                  }
              );
              if($scope.searchBonus.length == 0){*/
              var existe = false;
              angular.forEach(employee.bonus, function (employeeBonus) {
                if(employeeBonus.bonus.code == $scope.massiveBonus.type[index].code){
                  existe = true;
                }
              });
              if(existe == false){
                employee.bonus = _(employee).has('bonus') ? employee.bonus : [];
                $scope.assignedBonus = { 'bonus': angular.copy($scope.massiveBonus.type[index]) };
                $scope.assignedBonus.date = moment().format();
                $scope.assignedBonus.frequency = $scope.massiveBonus.frequencyBonus[index];
                employee.bonus.push($scope.assignedBonus);
                var bonus = { 'bonus': angular.copy(employee.bonus) };
                server.update('employee', bonus, employee._id).success(function (data) {
                  toastr[data.type](data.msg);
                });
              }
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
/*hojo redireccionar al home cuando guarde*/