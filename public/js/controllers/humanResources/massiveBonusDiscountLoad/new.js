'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {

    $scope.massiveBonus = {};
    $scope.departments = [];
    $scope.bonus = [];

    var typeBonus = '';

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });

    $scope.searchTypeBonus = function () {
      if($scope.massiveBonus.typeBonus == 'bonus')
        typeBonus = 'bonus';

      if($scope.massiveBonus.typeBonus == 'discounts')
        typeBonus = 'discounts';


      if(typeBonus){
        server.getAll(typeBonus).success(function (data) {
          $scope.type = data;
        });
      }
    };






    handlePanelAction();
  }
]);