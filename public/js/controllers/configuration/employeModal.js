'use strict';
angular.module('app').service('sharedProperties', function () {
    var property = '';

    //this.dataObj = {hola: property};

    this.setProperty =  function(value) {
        property = value;
        this.dataObj = {hola: property.envio};
        //alert(property.envio);
        return property;

    };

    this.getProperty =  function() {
        return property;

    };

  /* return {
        getProperty: function () {
            return property;
        },
        setProperty: function(value) {
            property = value;
           // alert(property);

        }
    };*/
}).controller('EmployeModalCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'Id_Depart',
  'sharedProperties',
  '$rootScope',
  function ($scope, $modalInstance, server, Id_Depart,sharedProperties, $rootScope) {
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

    getEmployees();

    //$scope.change = $scope.selectEmploye;

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
                  $scope.employeSelections[cuenta] = employe.identification;
                  cuenta++;
              }
          });

          alert($scope.employeSelections.length +' Empleados Seleccionados');

          //sharedProperties.setProperty('tercero');

          $rootScope.$broadcast('employees', { employeSelections: $scope.employeSelections });

          $modalInstance.dismiss();

      };

    $scope.cancel = function () {
        console.log($modalInstance);
      $modalInstance.dismiss();
    };

  }
]);