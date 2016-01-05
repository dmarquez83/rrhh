'use strict';
angular.module('app').controller('LiquidationCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'EmployeSelectionsModal',
  '$rootScope',
  function ($scope, $modalInstance, server, EmployeSelectionsModal, $rootScope) {
      $scope.less = 9.35;
      $scope.employeSelections = EmployeSelectionsModal;

       $scope.addBonus = function(bonus){
           var acumulador = 0;

           angular.forEach((bonus), function(datos){
               acumulador = acumulador + datos.bonus.value;
           });

       return acumulador;
       };

      $scope.addDiscount = function(discount){
          var acumulador = 0;
          angular.forEach((discount), function(datos){
              acumulador = acumulador + datos.discount.value;
          });
          return acumulador;
      };


      $scope.ReserveFund = function(employee){
          var reserve_fund =  (employee.grossSalary + $scope.addBonus(employee.bonus))/12 ;
          return reserve_fund;
      };
      //(employe.grossSalary + addBonus(employe.bonus))*(less/100)

      $scope.LessPersonal = function(employee){
          var less_personal =  (employee.grossSalary + $scope.addBonus(employee.bonus))*($scope.less/100) ;
          return less_personal;
      };

      $scope.revenues = function(employee){
          var revenues_ =  (employee.grossSalary + $scope.addBonus(employee.bonus) + $scope.ReserveFund(employee)) ;
          return revenues_;
      };

      $scope.discounts = function(employee){
          var discounts_ =  ($scope.LessPersonal(employee) + $scope.addDiscount(employee.discounts)) ;
          return discounts_;
      };

      $scope.totalToPay = function(employee){
          var totalToPay_ =  ($scope.revenues(employee) + $scope.discounts(employee)) ;
          return totalToPay_;
      };

      $scope.totalSalary = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.grossSalary;
          });
          return acumulador;
      };

      $scope.totalBonus = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              angular.forEach((datos.bonus), function(bonusEmp){
                  acumulador = acumulador + bonusEmp.bonus.value;
              });
          });
          return acumulador;
      };

      $scope.totalReserveFund = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + $scope.ReserveFund(datos);
          });
          return acumulador;
      };

      $scope.cancel = function () {
        $modalInstance.dismiss();
      };

  }
]);