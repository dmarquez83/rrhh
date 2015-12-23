'use strict';
angular.module('app').controller('EmployeModalCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  function ($scope, $modalInstance, server) {
    $scope.selectedEmploye = {};
    $scope.employees = [];

    var getCompanies = function(){
      server.post('getEmployees').success(function(result){
        $scope.employees = result;
      })
    }

    $scope.selectEmploye = function (employe) {
      server.post('changeCompany', employe).success(function (result) {
        console.log(result);
        $modalInstance.close();
      });
    };

    $scope.change = $scope.selectEmploye;

    $scope.cancel = function () {
      $modalInstance.dismiss();
    };

    getCompanies();

  }
]);