'use strict';
angular.module('app').controller('LiquidationCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'EmployeSelectionsModal',
  '$rootScope',
  function ($scope, $modalInstance, server, EmployeSelectionsModal, $rootScope) {

      $scope.employeSelections = EmployeSelectionsModal;

     /* $scope.addBonus = _.map(
          _.where($scope.employeSelections, {Fecha : row[0].Fecha}),
          function(person) {
              return { Codigo: person.Codigo, Fecha: person.Fecha, Hora: person.Hora};
          }
      );
*/

     /* $scope.addBonus = function(employeeId){

          $scope.addBonusEmp = _.map(
               _.where($scope.employeSelections, {_id : employeeId}),
               function(bonusemp) {
                   console.log(bonusemp);
                    return { BonusEmp: bonusemp.bonus};
               }
           );
          //console.log($scope.addBonusEmp);
          return 10;
      };*/

       $scope.addBonus = function(bonus){
           var acumulador = 0;

           angular.forEach((bonus), function(datos){
               acumulador = acumulador + datos.bonus.value;
           });

       return acumulador;
       };


      $scope.cancel = function () {
        $modalInstance.dismiss();
      };

  }
]);