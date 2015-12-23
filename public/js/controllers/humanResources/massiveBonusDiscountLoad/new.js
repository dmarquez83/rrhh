'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {

    $scope.massiveBonus = {};
    $scope.departments = {};

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });


    handlePanelAction();
  }
]);