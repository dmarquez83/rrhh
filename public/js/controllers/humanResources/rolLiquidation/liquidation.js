'use strict';
angular.module('app').controller('LiquidationCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  'EmployeSelectionsModal',
  'TypeSettlement',
  '$rootScope',
  function ($scope, $modalInstance, server, EmployeSelectionsModal, TypeSettlement, $location, $rootScope) {
      $scope.less = 9.35;
      $scope.employeSelections = EmployeSelectionsModal;
      $scope.typeSettlement = TypeSettlement;

      /*   $scope.monthSettlement = MonthSettlement;
      $scope.sinceDate = SinceDate;
      $scope.untilDate = UntilDate;*/
      //MonthSettlement,SinceDate,UntilDate,
      /* 'MonthSettlement',
       'SinceDate',
       'UntilDate',*/

       $scope.addBonus = function(bonus){
           var acumulador = 0;

           angular.forEach((bonus), function(datos){
              // acumulador = acumulador + datos.bonus.value;
               acumulador = acumulador + datos.value;
           });

       return acumulador;
       };

      $scope.addDiscount = function(discount){
          var acumulador = 0;
          angular.forEach((discount), function(datos){
             // acumulador = acumulador + datos.discount.value;
              acumulador = acumulador + datos.value;
          });
          return acumulador;
      };

      $scope.ReserveFund = function(employee){
          var reserve_fund =  (employee.grossSalary + $scope.addBonus(employee.bonus))/12 ;
          return reserve_fund;
      };

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
                  //acumulador = acumulador + bonusEmp.bonus.value;
                  acumulador = acumulador + bonusEmp.value;
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

      $scope.totalLessPersonal = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + $scope.LessPersonal(datos);
          });
          return acumulador;
      };

      $scope.totalDiscounts = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              angular.forEach((datos.discounts), function(discountEmp){
                  //acumulador = acumulador + discountEmp.discount.value;
                  acumulador = acumulador + discountEmp.value;
              });
          });
          return acumulador;
      };

      $scope.totalRevenues = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + $scope.revenues(datos);
          });
          return acumulador;
      };

      $scope.totalExpenditures = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + $scope.discounts(datos);
          });
          return acumulador;
      };

      $scope.totalToPayG = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + $scope.totalToPay(datos);
          });
          return acumulador;
      };

      $scope.savePreLiquidarTemp = function(){
          alert('entro');
          $scope.liquidation = [];
          $scope.liquidation_ = {};
          $scope.liquidation_.identification = '';
          $scope.liquidation_.name = '';
          $scope.liquidation_.department = '';
          $scope.liquidation_.grossSalary = '';
          $scope.liquidation_.bonus = '';
          $scope.liquidation_.commission = '';
          $scope.liquidation_.ReserveFund = '';
          $scope.liquidation_.LessPersonal = '';
          $scope.liquidation_.discount = '';
          $scope.liquidation_.advances = '';
          $scope.liquidation_.revenues = '';
          $scope.liquidation_.discounts_ = '';
          $scope.liquidation_.totalToPay = '';
          angular.forEach(($scope.employeSelections), function(employe){
              //$scope.preLiquidation={};
              //console.log(employe,'datos del empleado');
              $scope.liquidation_.identification = employe.identification;
              $scope.liquidation_.name = employe.names;
              $scope.liquidation_.department = employe.department.name;
              $scope.liquidation_.grossSalary = employe.grossSalary;
              $scope.liquidation_.bonus = $scope.addBonus(employe.bonus);
              $scope.liquidation_.commission = 0;
              $scope.liquidation_.ReserveFund = $scope.ReserveFund(employe);
              $scope.liquidation_.LessPersonal = $scope.LessPersonal(employe);
              $scope.liquidation_.discount = $scope.addDiscount(employe.discounts);
              $scope.liquidation_.advances = 0;
              $scope.liquidation_.revenues = $scope.revenues(employe);
              $scope.liquidation_.discounts_ = $scope.discounts(employe);
              $scope.liquidation_.totalToPay = $scope.totalToPay(employe);

              $scope.liquidation.push($scope.liquidation_);
              $scope.liquidation_ = {};
              //console.log($scope.liquidation_,'este');


          });
          //console.log($scope.liquidation,'este nuevo');

          server.save('paymenthRolesController', $scope.liquidation).success(function (data) {
              /*   console.log(data,'data');
               $scope.serverProcess = false;
               toastr[data.type](data.msg);
               if (data.type == 'success') {
               $scope.clean();
               }*/
              //solo esta registrando el ultimo empleado del siglo
          });
         // return acumulador;
      };

      $scope.cancel = function () {
         alertify.confirm("esta seguro que desea Cancelar? , se perderán los cambios.").set('onok', function() {
             //$modalInstance.close($location.path( "/" ));
             $modalInstance.dismiss();
          })

      };

  }
]);