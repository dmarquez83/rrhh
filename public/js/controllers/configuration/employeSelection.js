'use strict';
angular.module('app').controller('EmployeSelectionCtrl', [
  '$scope',
  '$modal',
  '$state',
  '$window',
  'server',
  function ($scope, $modal, server, Id_Depart, $state, $window) {

    $scope.openEmployeModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/massiveBonusDiscountLoad/employeSelection.html',
        controller: 'EmployeModalCtrl',
        size: 'lg',
        resolve: {
          Id_Depart: function() //scope del modal
          {
            console.log($scope.massiveBonus.department_id);
            return $scope.massiveBonus.department_id;

          }
        }
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };

  }
]);
