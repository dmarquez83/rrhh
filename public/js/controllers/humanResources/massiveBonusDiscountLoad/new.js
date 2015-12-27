'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'sharedProperties',
  function ($scope, documentValidate, server,sharedProperties) {

    $scope.massiveBonus = {};
    $scope.departments = [];
    $scope.bonusdiscounts = [];
    $scope.massiveBonus.typeBonus = [];
    $scope.type = [];
    $scope.massiveBonus.frequencyBonus = [];
    $scope.employeSelections = [];
    //$scope.prueba = sharedProperties.getProperty();

    $scope.datos = sharedProperties.dataObj;

    var typeBonus = '';

    $scope.save = function(){

      alert('guardo');


    };

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




    handlePanelAction();
  }
]);