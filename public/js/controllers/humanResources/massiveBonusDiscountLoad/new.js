'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  '$rootScope',
  function ($scope, documentValidate, server, $rootScope) {
    var typeBonus = '';
    $scope.massiveBonus = {};
    $scope.massiveBonus.typeBonus = [];
    $scope.massiveBonus.frequencyBonus = [];
    $scope.departments = [];
    $scope.bonusdiscounts = [];
    $scope.type = [];
    $scope.employeSelections = [];
    $scope.assignedDiscounts = {};


    $rootScope.$on('employees', function (event, values) {
        //console.log(values.employeSelections);
      $scope.employeSelections = values.employeSelections;
    });

    $scope.employeSelections =  $rootScope.employeSelections;

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });

    $scope.searchTypeBonus = function (index) {

      //alert($scope.massiveBonus.typeBonus[index]);

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


    $scope.addBonusDiscount = function() {
      $scope.bonusdiscounts.push({ hecho: true });
    };

    $scope.deleteBonusDiscount = function(index){
      $scope.bonusdiscounts.splice(index, 1);
    }

    $scope.save = function(){

      var index = 0;
      //console.log($scope.massiveBonus.type[index]);

      angular.forEach($scope.employeSelections, function (employee) {

        angular.forEach($scope.massiveBonus.typeBonus, function (type) {

          //alert(type);
          //alert($scope.massiveBonus.type[index]._id);
          //alert($scope.massiveBonus.frequencyBonus[index]);

          if(type == 'discounts'){
            employee.discounts = _(employee).has('discounts') ? employee.discounts : [];
            $scope.assignedDiscounts = angular.copy($scope.massiveBonus.type[index]);
            $scope.assignedDiscounts.date = moment().format();
            employee.discounts.push($scope.assignedDiscounts);
            employee.discounts.push($scope.massiveBonus.frequencyBonus[index]);
            var discounts = { 'discounts': angular.copy(employee.discounts) };
            //console.log(discounts);
            server.update('employee', discounts, employee._id).success(function (data) {
            });
            //console.log(server);
          }

          index++;

        });

      });




    };


    handlePanelAction();
  }
]);