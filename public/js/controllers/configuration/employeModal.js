'use strict';
angular.module('app').controller('EmployeModalCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'Id_Depart',
  function ($scope, $modalInstance, server, Id_Depart ) {
    //$scope.selectedEmploye = {};
    $scope.employees = [];
    $scope.id_depart = Id_Depart;

    $scope.employeSelections = [];

    var getEmployees = function(){
      server.post('getEmployees').success(function(result){

        if($scope.id_depart)
            $scope.employees = _(result).where({ 'department_id':  $scope.id_depart });
        else
            $scope.employees = result;
      })
    }

    $scope.change = $scope.selectEmploye;

      $scope.checkAll = function () {
          if ($scope.selectedAll) {
              $scope.selectedAll = true;
          } else {
              $scope.selectedAll = false;
          }
          angular.forEach($scope.employees, function (employe) {
              employe.Selected = $scope.selectedAll;
          });

      };

      $scope.saveEmploye = function () {

          var cuenta = 0;
          angular.forEach($scope.employees, function (employe) {
              if(employe.Selected){
                  cuenta++;
                  //alert(employe.identification);
              }

          });

          alert(cuenta);

      };


    $scope.cancel = function () {
      $modalInstance.dismiss();
    };

    getEmployees();

  }
]);