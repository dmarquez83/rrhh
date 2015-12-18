'use strict';
angular.module('app').controller('CompanySelectionCtrl', [
  '$scope',
  '$modal',
  '$state',
  '$window',
  'server',
  function ($scope, $modal, $state, $window, server) {

    $scope.openCompanyModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/configuration/companySelection.html',
        controller: 'CompanyModalCtrl',
        size: 'lg'
      });
      modalInstance.result.then(function () {
        $window.location.reload();
      });
    };

  }
]);
