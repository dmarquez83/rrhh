'use strict';
angular.module('app').controller('LiquidationCtrl', [
  '$scope',
  '$state',
  '$modalInstance',
  'server',
  '$rootScope',
  'SweetAlert',
  'EmployeSelectionsModal',
  'TypeSettlement',
  'MonthSettlement',
  'SinceDate',
  'UntilDate',
  'Status',
  function ($scope, $state, $modalInstance, server, $rootScope, SweetAlert, EmployeSelectionsModal, TypeSettlement,MonthSettlement,SinceDate,UntilDate,Status, $location) {
      $scope.less = 9.35;
      $scope.employeSelections = EmployeSelectionsModal;
      $scope.typeSettlement = TypeSettlement;

      $scope.monthSettlement = MonthSettlement;
      $scope.sinceDate = SinceDate;
      $scope.untilDate = UntilDate;
      $scope.status =Status;

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
          if($scope.mesSel=='12') $scope.mes= 'Diciembre';
      }else{
          $scope.tipo = 'Quincenal';
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
          if($scope.mesSel=='23' ||  $scope.mesSel=='24') $scope.mes= 'Diciembre';
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
         /* $scope.typeSettlement='';
          $scope.rolLiquidation.monthSettlement = '';
          $scope.rolLiquidation.firstDay='';
          $scope.rolLiquidation.lastDay='';
          $scope.employeSelections = [];
          $scope.liquidationArray= [];*/
      };

      $scope.savePreLiquidarTemp = function(){
          $scope.liquidationArray= [];
          $scope.liquidation_ = {};


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
              $scope.liquidation_.monthliquidation = $scope.monthSettlement;
              $scope.liquidation_.sinceDate = $scope.sinceDate;
              $scope.liquidation_.untilDate = $scope.untilDate;
              $scope.liquidation_.typeSettlement = $scope.typeSettlement;
              $scope.liquidation_.mesSel = $scope.mesSel;

              $scope.liquidationArray.push($scope.liquidation_);
              $scope.liquidation_ = {};

          });

          server.save('paymenthRolesController', $scope.liquidationArray).success(function (data) {
              toastr[data.type]('Pre-Liquidación de Rol satisfactoria');
              $modalInstance.dismiss();
              $state.reload();
              $scope.clean();
          });
      };

      $scope.savePreLiquidar = function(){

          SweetAlert.swal({
                  title: "Esta seguro que desea liquidar?",
                  text: "una vez hecha la liquidación no se podrán revertir los cambios",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",confirmButtonText: "Si, Liquidar",
                  cancelButtonText: "No, Liquidar",
                  closeOnConfirm: true,
                  closeOnCancel: true },
              function(isConfirm){
                  if (isConfirm) {
                  $scope.liquidationArray= [];
                  $scope.liquidation_ = {};

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
                      $scope.liquidation_.monthliquidation = $scope.monthSettlement;
                      $scope.liquidation_.sinceDate = $scope.sinceDate;
                      $scope.liquidation_.untilDate = $scope.untilDate;
                      $scope.liquidation_.typeSettlement = $scope.typeSettlement;
                      $scope.liquidation_.mesSel = $scope.mesSel;

                      employe.discounts = _(employe).has('discounts') ? employe.discounts : [];
                      employe.bonus = _(employe).has('bonus') ? employe.bonus : [];

                      $scope.liquidation_.discount = angular.copy(employe.discounts);
                      $scope.liquidation_.bonus = angular.copy(employe.bonus);

                      //var paymenthRole = { 'paymenthRole':  {'discount': angular.copy(employe.discounts), 'bonus': angular.copy(employe.bonus) }};

                      employe.paymentRole.push($scope.liquidation_);

                      var paymenthRole = { 'paymentRole': angular.copy(employe.paymentRole) };

                      server.update('employee', paymenthRole, employe._id).success(function (data) {
                          $scope.deleteBonus(employe);
                          $scope.deleteDiscount(employe);
                      });

                      $scope.liquidationArray.push($scope.liquidation_);
                      $scope.liquidation_ = {};

                  });

                  server.save('paymenthRolesController', $scope.liquidationArray).success(function (data) {

                      if (data.type == 'success') {
                          toastr[data.type]('Liquidación de Rol satisfactoria');
                          $modalInstance.dismiss();
                          $state.reload();
                      }else{
                          toastr[data.type]('No se pudo realizar la Liquidación de Rol');
                          $modalInstance.dismiss();
                          $state.reload();
                          $scope.clean();
                      }
                  });
              }
      });
  };

      $scope.savePreLiquidarS = function(){
          $scope.liquidation_ = {};
          server.post('getEmployees').success(function(result){
              $scope.employees = (result);
          });
          SweetAlert.swal({
                  title: "Esta seguro que desea liquidar?",
                  text: "una vez hecha la liquidación no se podrán revertir los cambios",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",confirmButtonText: "Si, Liquidar",
                  cancelButtonText: "No, Liquidar",
                  closeOnConfirm: true,
                  closeOnCancel: true },
              function(isConfirm){
                  if (isConfirm) {
                      angular.forEach(($scope.employeSelections), function (employe) {
                          $scope.liquidation_.status = 'liquidation';
                          employe.status = 'liquidation';

                          server.update('paymenthRolesController', $scope.liquidation_, employe._id).success(function (data) {


                          });
                          $scope.employeesLiquidar = _($scope.employees).where({'identification': employe.identification});

                          employe.discounts = _($scope.employeesLiquidar[0]).has('discounts') ? $scope.employeesLiquidar[0].discounts : [];
                          employe.bonus = _($scope.employeesLiquidar[0]).has('bonus') ? $scope.employeesLiquidar[0].bonus : [];

                          $scope.liquidation_.discount = angular.copy(employe.discounts);
                          $scope.liquidation_.bonus = angular.copy(employe.bonus);

                          $scope.employeesLiquidar[0].paymentRole.push(employe);

                          var paymenthRole = { 'paymentRole': angular.copy($scope.employeesLiquidar[0].paymentRole) };

                          server.update('employee', paymenthRole, $scope.employeesLiquidar[0]._id).success(function (data) {

                          });

                          $scope.deleteBonus($scope.employeesLiquidar[0]);
                          $scope.deleteDiscount($scope.employeesLiquidar[0]);

                          $scope.employeesLiquidar='';

                      });

                      toastr.success('Liquidación de Rol satisfactoria');
                      $modalInstance.dismiss();
                      $state.reload();
                      $scope.clean();
                  }
              });
      };

      $scope.eliminarLiq = function() {
          SweetAlert.swal({
                  title: "Está seguro de eliminar esta Preliquidacion?",
                  text: "Si elimina la Preliquidacion no lo podrá recuperar",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
                  cancelButtonText: "No, cancelar",
                  closeOnConfirm: true,
                  closeOnCancel: true },
              function(isConfirm){
                  if (isConfirm) {
                      $scope.serverProcess = true;
                      angular.forEach(($scope.employeSelections), function(employeSelections){
                          server.delete('paymenthRolesController', employeSelections._id).success(function(result){
                              SweetAlert.swal("Eliminado!", result.msg, result.type);
                           })
                      });
                      $scope.serverProcess = false;
                      $modalInstance.dismiss();
                      $state.reload();

                  }
              });
      };

      $scope.cancel = function () {
          SweetAlert.swal({
                  title: "esta seguro que desea Cancelar? ",
                  text: "se perderán los cambios",
                  type: "warning",
                  showCancelButton: false,
                  confirmButtonColor: "#DD6B55",confirmButtonText: "Ok",
                  cancelButtonText: "",
                  closeOnConfirm: true,
                  closeOnCancel: true },
              function(isConfirm){
                  if (isConfirm) {
                      $modalInstance.dismiss();
                      $state.reload();
                  }
              });
      };


      $scope.cerrar = function () {
          $modalInstance.dismiss();
      };

  }
]);