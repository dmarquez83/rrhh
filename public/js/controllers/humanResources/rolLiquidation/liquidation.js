'use strict';
angular.module('app').controller('LiquidationCtrl', [
  '$scope',
  '$modalInstance',
  'server',
  '$rootScope',
  'EmployeSelectionsModal',
  'TypeSettlement',
  'MonthSettlement',
  'SinceDate',
  'UntilDate',
  function ($scope, $modalInstance, server, $rootScope, EmployeSelectionsModal, TypeSettlement,MonthSettlement,SinceDate,UntilDate, $location) {
      $scope.less = 9.35;
      $scope.employeSelections = EmployeSelectionsModal;
      $scope.typeSettlement = TypeSettlement;

      $scope.monthSettlement = MonthSettlement;
      $scope.sinceDate = SinceDate;
      $scope.untilDate = UntilDate;

      if ($scope.typeSettlement=='monthly'){
          $scope.tipo = 'Mensual';
          $scope.mesSel = $scope.monthSettlement;
          if($scope.mesSel=='1') $scope.mes= 'Enero';
          if($scope.mesSel=='2') $scope.mes= 'Febrero';
          if($scope.mesSel=='3') $scope.mes= 'Marzo';
          if($scope.mesSel=='4') $scope.mes= 'Abril';
          if($scope.mesSel=='5') $scope.mes= 'Mayo';
          if($scope.mesSel=='6') $scope.mes= 'Junio';
          if($scope.mesSel=='7') $scope.mes= 'Julio';
          if($scope.mesSel=='8') $scope.mes= 'Agosto';
          if($scope.mesSel=='9') $scope.mes= 'Septiembre';
          if($scope.mesSel=='10') $scope.mes= 'Octubre';
          if($scope.mesSel=='11') $scope.mes= 'Noviembre';
          if($scope.mesSel=='12') $scope.mes= 'Dicembre';
      }else{
          $scope.tipo = 'Quincinal';
          $scope.mesSel = $scope.monthSettlement;
          if($scope.mesSel=='1' ||  $scope.mesSel=='2') $scope.mes= 'Enero';
          if($scope.mesSel=='3' ||  $scope.mesSel=='4') $scope.mes= 'Febrero';
          if($scope.mesSel=='5' ||  $scope.mesSel=='6') $scope.mes= 'Marzo';
          if($scope.mesSel=='7' ||  $scope.mesSel=='8') $scope.mes= 'Abril';
          if($scope.mesSel=='9' ||  $scope.mesSel=='10') $scope.mes= 'Mayo';
          if($scope.mesSel=='11' ||  $scope.mesSel=='12') $scope.mes= 'Junio';
          if($scope.mesSel=='13' ||  $scope.mesSel=='14') $scope.mes= 'Julio';
          if($scope.mesSel=='15' ||  $scope.mesSel=='16') $scope.mes= 'Agosto';
          if($scope.mesSel=='17' ||  $scope.mesSel=='18') $scope.mes= 'Septiembre';
          if($scope.mesSel=='19' ||  $scope.mesSel=='20') $scope.mes= 'Octubre';
          if($scope.mesSel=='21' ||  $scope.mesSel=='22') $scope.mes= 'Noviembre';
          if($scope.mesSel=='23' ||  $scope.mesSel=='24') $scope.mes= 'Dicembre';
      }


      $scope.deleteBonus = function(employe){
          var i=0;
          var id = employe._id;
          angular.forEach((employe.bonus), function(datos){
              var objDate = new Date(datos.date),
                  locale = "en-us",
                  month = objDate.toLocaleString(locale, { month: "2-digit" });
              if(datos.frequency=='once' &&  (parseInt(month) == parseInt($scope.monthSettlement)) ){
                  //console.log('entro',datos,i);
                  employe.bonus.splice(i, 1);
                  var bonus = { 'bonus': angular.copy(employe.bonus) };
                  server.update('employee', bonus, id).success(function (data) {
                  });
              }
              i++;
          });
      };

      $scope.deleteDiscount = function(employe){
          var i=0;
          var id = employe._id;
          angular.forEach((employe.discounts), function(datos){
              var objDate = new Date(datos.date),
                  locale = "en-us",
                  month = objDate.toLocaleString(locale, { month: "2-digit" });
              if(datos.frequency=='once' &&  (parseInt(month) == parseInt($scope.monthSettlement)) ){
                  //console.log('entro',datos,i);
                  employe.discounts.splice(i, 1);
                  var discounts = { 'discounts': angular.copy(employe.discounts) };
                  server.update('employee', discounts, id).success(function (data) {
                  });
              }
              i++;
          });
      };

       $scope.addBonus = function(bonus){
           var acumulador = 0;

           angular.forEach((bonus), function(datos){
               var objDate = new Date(datos.date),
                   locale = "en-us",
                   month = objDate.toLocaleString(locale, { month: "2-digit" });
               //console.log('mes',parseInt(month),parseInt($scope.monthSettlement),'frecuencia',datos.frequency);
               if(datos.frequency=='once' &&  (parseInt(month) == parseInt($scope.monthSettlement)) ){
                   return acumulador = acumulador + datos.bonus.value;
               }else{
                   if(datos.frequency=='monthly'){
                       return acumulador = acumulador + datos.bonus.value;
                   }
               } // acumulador = acumulador + datos.value;
           });

       return acumulador;
       };

      $scope.addDiscount = function(discount){
          var acumulador = 0;
          angular.forEach((discount), function(datos){
              var objDate = new Date(datos.date),
                  locale = "en-us",
                  month = objDate.toLocaleString(locale, { month: "2-digit" });
              if(datos.frequency=='once' &&  (parseInt(month) == parseInt($scope.monthSettlement)) ){
                  return acumulador = acumulador + datos.discount.value;
              }else{
                  if(datos.frequency=='monthly'){
                      return acumulador = acumulador + datos.discount.value;
                  }
              }
          });
          return acumulador;
      };

      $scope.ReserveFund = function(employee){

          var DateTime = new Date();
          var date = DateTime.getFullYear();
          var objDate = new Date(employee.lastStateDate),
              locale = "en-us",
              year = objDate.toLocaleString(locale, { year: "numeric" });
          var antiguedad = parseInt(date) - parseInt(year);
          if(antiguedad>1){
              var reserve_fund =  (employee.grossSalary + $scope.addBonus(employee.bonus))/12 ;
          }else{
              var reserve_fund =0;
          }

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
              acumulador = acumulador + $scope.addBonus(datos.bonus);
            //  acumulador = acumulador + bonusEmp.value;
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
              acumulador = acumulador + $scope.addDiscount(datos.discounts);
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

      /*aqui total cuando ya esta guardada*/
      $scope.totalSalaryS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.grossSalary;
          });
          return acumulador;
      };

      $scope.totalBonusS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.bonus;
              //  acumulador = acumulador + bonusEmp.value;
          });
          return acumulador;
      };

      $scope.totalReserveFundS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.ReserveFund;
          });
          return acumulador;
      };

      $scope.totalLessPersonalS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.LessPersonal;
          });
          return acumulador;
      };

      $scope.totalDiscountsS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.discount;
          });
          return acumulador;
      };

      $scope.totalRevenuesS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.revenues;
          });
          return acumulador;
      };

      $scope.totalExpendituresS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.discounts_;
          });
          return acumulador;
      };

      $scope.totalToPayGS = function(){
          var acumulador = 0;
          angular.forEach(($scope.employeSelections), function(datos){
              acumulador = acumulador + datos.totalToPay;
          });
          return acumulador;

      };
      /*fin*/


      $scope.clean = function () {
          $scope.typeSettlement='';
          $scope.monthSettlement = '';
          $scope.sinceDate='';
          $scope.untilDate='';
          $scope.employeSelections = [];
          $scope.liquidationArray= [];
      };

      $scope.savePreLiquidarTemp = function(){
          $scope.liquidationArray= [];
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
          $scope.liquidation_.status = '';
          $scope.liquidation_.monthliquidation = '';

          if($scope.typeSettlement=='monthly'){
              $scope.mesSel = $scope.monthSettlement;
          }else{
              $scope.mesSel = $scope.monthSettlement;
              if($scope.mesSel=='1' ||  $scope.mesSel=='2') $scope.mesSel= 1;
              if($scope.mesSel=='3' ||  $scope.mesSel=='4') $scope.mesSel= 2;
              if($scope.mesSel=='5' ||  $scope.mesSel=='6') $scope.mesSel= 3;
              if($scope.mesSel=='7' ||  $scope.mesSel=='8') $scope.mesSel= 4;
              if($scope.mesSel=='9' ||  $scope.mesSel=='10') $scope.mesSel= 5;
              if($scope.mesSel=='11' ||  $scope.mesSel=='12') $scope.mesSel= 6;
              if($scope.mesSel=='13' ||  $scope.mesSel=='14') $scope.mesSel= 7;
              if($scope.mesSel=='15' ||  $scope.mesSel=='16') $scope.mesSel= 8;
              if($scope.mesSel=='17' ||  $scope.mesSel=='18') $scope.mesSel= 9;
              if($scope.mesSel=='19' ||  $scope.mesSel=='20') $scope.mesSel= 10;
              if($scope.mesSel=='21' ||  $scope.mesSel=='22') $scope.mesSel= 11;
              if($scope.mesSel=='23' ||  $scope.mesSel=='24') $scope.mesSel= 12;

          }

          angular.forEach(($scope.employeSelections), function(employe){
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
              $scope.liquidation_.status = 'preliquidation';
              $scope.liquidation_.monthliquidation = $scope.mesSel;
              $scope.liquidation_.sinceDate = $scope.sinceDate;
              $scope.liquidation_.untilDate = $scope.untilDate;

              $scope.liquidationArray.push($scope.liquidation_);
              $scope.liquidation_ = {};

          });

          server.save('paymenthRolesController', $scope.liquidationArray).success(function (data) {
              toastr[data.type]('Liquidación de Rol satisfactoria');
          });

          $rootScope.$broadcast('monthliquidation', { monthSelections: $scope.mesSel});
          $modalInstance.dismiss();
      };

      $scope.savePreLiquidar = function(){

          alertify.confirm("Esta seguro que desea liquidar rol, una vez hecha la liquidación no se podrán revertir los cambios..",
              function(){
                  $scope.liquidationArray= [];
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
                  $scope.liquidation_.status = '';
                  $scope.liquidation_.monthliquidation = '';

                  if($scope.typeSettlement=='monthly'){
                      $scope.mesSel = $scope.monthSettlement;
                  }else{
                      $scope.mesSel = $scope.monthSettlement;
                      if($scope.mesSel=='1' ||  $scope.mesSel=='2') $scope.mesSel= 1;
                      if($scope.mesSel=='3' ||  $scope.mesSel=='4') $scope.mesSel= 2;
                      if($scope.mesSel=='5' ||  $scope.mesSel=='6') $scope.mesSel= 3;
                      if($scope.mesSel=='7' ||  $scope.mesSel=='8') $scope.mesSel= 4;
                      if($scope.mesSel=='9' ||  $scope.mesSel=='10') $scope.mesSel= 5;
                      if($scope.mesSel=='11' ||  $scope.mesSel=='12') $scope.mesSel= 6;
                      if($scope.mesSel=='13' ||  $scope.mesSel=='14') $scope.mesSel= 7;
                      if($scope.mesSel=='15' ||  $scope.mesSel=='16') $scope.mesSel= 8;
                      if($scope.mesSel=='17' ||  $scope.mesSel=='18') $scope.mesSel= 9;
                      if($scope.mesSel=='19' ||  $scope.mesSel=='20') $scope.mesSel= 10;
                      if($scope.mesSel=='21' ||  $scope.mesSel=='22') $scope.mesSel= 11;
                      if($scope.mesSel=='23' ||  $scope.mesSel=='24') $scope.mesSel= 12;

                  }

                  angular.forEach(($scope.employeSelections), function(employe){
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
                      $scope.liquidation_.status = 'liquidation';
                      $scope.liquidation_.monthliquidation = $scope.mesSel;
                      $scope.liquidation_.sinceDate = $scope.sinceDate;
                      $scope.liquidation_.untilDate = $scope.untilDate;

                      $scope.liquidationArray.push($scope.liquidation_);
                      $scope.liquidation_ = {};

                      employe.discounts = _(employe).has('discounts') ? employe.discounts : [];
                      employe.bonus = _(employe).has('bonus') ? employe.bonus : [];
                      var paymenthRole = { 'paymenthRole':  {'discount': angular.copy(employe.discounts), 'bonus': angular.copy(employe.bonus) }};
                      server.update('employee', paymenthRole, employe._id).success(function (data) {
                          $scope.deleteBonus(employe);
                          $scope.deleteDiscount(employe);
                      });

                  });

                  server.save('paymenthRolesController', $scope.liquidationArray).success(function (data) {
                      if (data.type == 'success') {
                          toastr[data.type]('Liquidación de Rol satisfactoria');
                          $scope.clean();
                          $rootScope.$broadcast('monthliquidation', { monthSelections: $scope.mesSel});
                          $modalInstance.dismiss();
                      }else{
                          toastr[data.type]('No se pudo realizar la Liquidación de Rol');
                          $modalInstance.dismiss();
                      }

                  });

              },
              function(){
                  alertify.error('Cancel');
              });


      };

      $scope.savePreLiquidarS = function(){

          $scope.liquidationArray= [];
          $scope.liquidation_ = {};

          alertify.confirm("Esta seguro que desea liquidar rol, una vez hecha la liquidación no se podrán revertir los cambios..",
              function(){
                  angular.forEach(($scope.employeSelections), function(employe){
                      $scope.liquidation_.identification = employe.identification;
                      $scope.liquidation_.name = employe.name;
                      $scope.liquidation_.department = employe.department;
                      $scope.liquidation_.grossSalary = employe.grossSalary;
                      $scope.liquidation_.bonus = employe.bonus;
                      $scope.liquidation_.commission = employe.commission;
                      $scope.liquidation_.ReserveFund = employe.ReserveFund;
                      $scope.liquidation_.LessPersonal = employe.LessPersonal;
                      $scope.liquidation_.discount = employe.discount;
                      $scope.liquidation_.advances = employe.advances;
                      $scope.liquidation_.revenues = employe.revenues;
                      $scope.liquidation_.discounts_ = employe.discounts_;
                      $scope.liquidation_.totalToPay = employe.totalToPay;
                      $scope.liquidation_.status = 'liquidation';
                      $scope.liquidation_.monthliquidation = employe.monthliquidation;
                      $scope.liquidation_.sinceDate = employe.sinceDate;
                      $scope.liquidation_.untilDate = employe.untilDate;

                      server.update('paymenthRolesController', $scope.liquidation_,employe._id).success(function (data) {

                      });
                      $scope.liquidationArray= [];
                      $scope.liquidation_ = {};

                      //ojo con este me invertio todo los datos de posicion

                      //aqui debo buscar por cada empleado sus datos para poder limpiar los bonos y descuentos


                     /* employe.discounts = _(employe).has('discounts') ? employe.discounts : [];
                      employe.bonus = _(employe).has('bonus') ? employe.bonus : [];
                      var paymenthRole = { 'paymenthRole':  {'discount': angular.copy(employe.discounts), 'bonus': angular.copy(employe.bonus) }};
                      server.update('employee', paymenthRole, employe._id).success(function (data) {
                          $scope.deleteBonus(employe);
                          $scope.deleteDiscount(employe);
                      });*/

                  });


                  $rootScope.$broadcast('monthliquidation', { monthSelections: $scope.mesSel});
                  $modalInstance.dismiss();
              },
              function(){
                  alertify.error('Cancel');
              });


      };

      $scope.cancel = function () {
         alertify.confirm("esta seguro que desea Cancelar? , se perderán los cambios.",
             function() {
             //$modalInstance.close($location.path( "/" ));/*aqui buscar o preguntar como redirecciono a otra ruta
                 $modalInstance.dismiss();
             },
             function() {
                 alertify.error('Cancel');
          });

      };

  }
]);