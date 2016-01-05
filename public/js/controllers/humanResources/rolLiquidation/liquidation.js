'use strict';
angular.module('app').controller('LiquidationCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'EmployeSelectionsModal',
  '$rootScope',
  function ($scope, $modalInstance, server, EmployeSelectionsModal, $rootScope) {

      $scope.employeSelections = EmployeSelectionsModal;

      $scope.cancel = function () {
        $modalInstance.dismiss();
      };

  }
]);