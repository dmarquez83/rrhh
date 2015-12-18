'use strict';
angular.module('app').controller('CompanyModalCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  function ($scope, $modalInstance, server) {
    $scope.selectedCompany = {};
    $scope.companies = [];

    var getCompanies = function(){
      server.post('getCompanies').success(function(result){
        $scope.companies = result;
      })
    }

    $scope.selectCompany = function (company) {
      server.post('changeCompany', company).success(function (result) {
        console.log(result);
        $modalInstance.close();
      });
    };

    $scope.change = $scope.selectCompany;

    $scope.cancel = function () {
      $modalInstance.dismiss();
    };

    getCompanies();

  }
]);