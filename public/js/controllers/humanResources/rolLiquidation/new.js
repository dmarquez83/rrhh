'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
    '$scope',
    '$modal',
    'server',
    '$rootScope',
    function ($scope,$modal,server,$rootScope) {
        $scope.rolLiquidation = {};
        $scope.rolLiquidation.departmentName = '';
        $scope.employees = [];
        $scope.employeSelections = [];
        $scope.countEmployee = 0;
        $scope.prueba = 'hola';
        $scope.paymenthroles=[];
        $scope.resumenpaymenthroles=[];
        $scope.statusPreLiquidation=true;
        $scope.cleanform = false;

        $rootScope.$on('employees', function (event, values) {
            $scope.employeSelections = values.employeSelections;
        });

        $rootScope.$on('cleanform', function (event, values) {
            $scope.cleanform = values.clean;

            if(values.clean){
                $scope.typeSettlement='';
                $scope.rolLiquidation.monthSettlement = '';
                $scope.rolLiquidation.firstDay='';
                $scope.rolLiquidation.lastDay='';
                $scope.employeSelections = [];
                $scope.listarRoles();
            }

        });

      $scope.listarRoles = function(){

           server.post('getPaymenthRoles').success(function(result){
               $scope.resumenpaymenthroles=[];
               $scope.paymenthroles = _.groupBy(_(result).where({ 'status': 'preliquidation'}), 'monthliquidation');
               angular.forEach(($scope.paymenthroles), function(row) {
                   var total = 0;
                   angular.forEach((row), function(det) {
                       total = total +  parseFloat(det.totalToPay);
                   });
                   $scope.resumenpaymenthroles.push({Fecha:row[0].sinceDate, Cantidad:row.length, Total:total, Tipo:row[0].typeSettlement, Mes:row[0].monthliquidation, DatePreLiq: row});
               });
          });
      }


        $scope.searchEmployeAct = function () {

            //console.log($scope.selectedAllEmp,'estatus')

            if($scope.rolLiquidation.monthSettlement) {
                if($scope.selectedAllEmp){
                    server.post('getEmployees').success(function (result) {
                        $scope.employees = _(result).where({'status': 'Activo'});
                        $rootScope.$broadcast('employees', {employeSelections: $scope.employees});
                    });

                    $rootScope.$on('employees', function (event, values) {
                        $scope.employeSelections = values.employeSelections;
                    });
                }else{
                    $scope.employeSelections=[];
                }

            }else{
                $scope.selectedAllEmp=false;
                toastr.error('Seleccione el Mes de Liquidación para poder seleccionar los empleados');
            }
        };


        $scope.listFechas= function(){

            $scope.rolLiquidation.firstDay='';
            $scope.rolLiquidation.lastDay='';
            $scope.date = new Date();
            $scope.anhoAct = $scope.date.getFullYear();

            if($scope.typeSettlement=='monthly'){

                $scope.mesSel = $scope.rolLiquidation.monthSettlement;
                var objDate1 = new Date($scope.anhoAct, $scope.mesSel - 1, 1);
                $scope.rolLiquidation.firstDay =  new Intl.DateTimeFormat().format(objDate1);
                var objDate2 = new Date($scope.anhoAct,$scope.mesSel, 0);
                $scope.rolLiquidation.lastDay  = new Intl.DateTimeFormat().format(objDate2);

            }else{
               if(parseInt($scope.rolLiquidation.monthSettlement) % 2 == 0)
                {
                    var inicio =15;
                    var fin =0;
                    var resta = 0;
                }
                else
                {
                    var inicio =1;
                    var fin =15;
                    var resta = 1;
                }

                $scope.mesSel = $scope.rolLiquidation.monthSettlement;
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

                var objDate1 = new Date($scope.anhoAct, $scope.mesSel - 1, inicio);
                $scope.rolLiquidation.firstDay =  new Intl.DateTimeFormat().format(objDate1);
                var objDate2 = new Date($scope.anhoAct,$scope.mesSel - resta, fin);
                $scope.rolLiquidation.lastDay  = new Intl.DateTimeFormat().format(objDate2);
            }

            server.post('getPaymenthRoles').success(function(result){
                $scope.paymenthroles = _(result).where({ 'monthliquidation':  $scope.rolLiquidation.monthSettlement, 'status': 'liquidation' });
                if($scope.paymenthroles.length>0){
                    toastr.error('Ya ha sido hecha la liquidación de esta Fecha');
                    $scope.rolLiquidation.monthSettlement = '';
                    $scope.rolLiquidation.firstDay = '';
                    $scope.rolLiquidation.lastDay = '';
                }

                $scope.paymenthroles = _(result).where({ 'monthliquidation':  $scope.rolLiquidation.monthSettlement, 'status': 'preliquidation' });
                if($scope.paymenthroles.length>0){
                    toastr.warning('La Fecha que selecciono ya fue pre-liquidado consulte en el resumen ');
                    $scope.rolLiquidation.monthSettlement = '';
                    $scope.rolLiquidation.firstDay = '';
                    $scope.rolLiquidation.lastDay = '';
                }
            });
            $scope.statusPreLiquidation=true;
        };

        server.post('getPaymenthRoles').success(function(result){
            $scope.resumenpaymenthroles=[];
            $scope.paymenthroles = _.groupBy(_(result).where({ 'status': 'preliquidation'}), 'monthliquidation');
             angular.forEach(($scope.paymenthroles), function(row) {
             var total = 0;
                  angular.forEach((row), function(det) {
                    total = total +  parseFloat(det.totalToPay);
                  });
                 $scope.resumenpaymenthroles.push({Fecha:row[0].sinceDate, Cantidad:row.length, Total:total, Tipo:row[0].typeSettlement, Mes:row[0].monthliquidation, DatePreLiq: row});
             });
        });


        server.post('getPaymenthRoles').success(function(result){
            $scope.resumenpaymenthrolesLiq=[];
            $scope.paymenthrolesLiq = _.groupBy(_(result).where({ 'status': 'liquidation'}), 'monthliquidation');
            angular.forEach(($scope.paymenthrolesLiq), function(row) {
                var total = 0;
                angular.forEach((row), function(det) {
                    total = total +  parseFloat(det.totalToPay);
                });
                $scope.resumenpaymenthrolesLiq.push({Fecha:row[0].sinceDate, Cantidad:row.length, Total:total, Tipo:row[0].typeSettlement, Mes:row[0].monthliquidation, DatePreLiq: row});
            });
        });

        handlePanelAction();
    }

]);
