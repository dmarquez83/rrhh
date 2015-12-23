'use strict';
angular.module('app').controller('EmployeModalCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'Id_Depart',
  function ($scope, $modalInstance, server, Id_Depart ) {
    $scope.selectedEmploye = {};
    $scope.employees = [];

    $scope.id_depart = Id_Depart;

    var getEmployees = function(){
      server.post('getEmployees').success(function(result){
        //$scope.employees = result;
        $scope.employees = _(result).where({ 'department_id':  $scope.id_depart });
      })
    }

    $scope.change = $scope.selectEmploye;

    $scope.cancel = function () {
      $modalInstance.dismiss();
    };

    getEmployees();

  }
]);