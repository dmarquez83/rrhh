'use strict';
angular.module('app').controller('EmployeSelectionCtrl', [
  '$scope',
  '$modal',
  '$state',
  '$window',
  'server',
  function ($scope, $modal, $state, $window, server) {

    $scope.openEmployeModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/massiveBonusDiscountLoad/employeSelection.html',
        controller: 'EmployeModalCtrl',
        size: 'lg'
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };

  }
]);
