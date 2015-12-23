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

        if($scope.id_depart)
            $scope.employees = _(result).where({ 'department_id':  $scope.id_depart });
        else
            $scope.employees = result;
      })
    }

    $scope.change = $scope.selectEmploye;

    $scope.cancel = function () {
      $modalInstance.dismiss();
    };

    getEmployees();

  }
]);