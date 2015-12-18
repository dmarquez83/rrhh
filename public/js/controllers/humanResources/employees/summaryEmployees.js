'use strict';
angular.module('app').controller('SummaryEmployeesCtrl', [
  '$scope',
  '$state',
  'server',
  'transferData',
  function ($scope, $state, server, transferData) {
    server.getAll('employee').success(function (data) {
      $scope.employees = data;
    });
    $scope.openProfile = function (employee) {
      transferData.data = { 'employee': employee };
      $state.go('humanResources.employeeProfile');
    };
  }
]);