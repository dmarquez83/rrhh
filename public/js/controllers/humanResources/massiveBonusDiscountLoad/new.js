'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  '$rootScope',
  function ($scope, documentValidate, server, $rootScope) {
    var typeBonus = '';
    $scope.massiveBonus = {};
    $scope.departments = [];
    $scope.bonusdiscounts = [];
    $scope.massiveBonus.typeBonus = [];
    $scope.type = [];
    $scope.massiveBonus.frequencyBonus = [];
    $scope.employeSelections = [];


    $rootScope.$on('employees', function (event, values) {
      console.log(values.employeSelections);
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
      $scope.bonusdiscounts.push({ });
    };

    $scope.deleteBonusDiscount = function(index){
      $scope.bonusdiscounts.splice(index, 1);
    }

    $scope.save = function(){

      server.post('getEmployees').success(function(result){

        $scope.employees = result;
      });


      angular.forEach($scope.employees, function (employe) {

        alert(employe.identification);
        alert(index);

      });


    };


    handlePanelAction();
  }
]);