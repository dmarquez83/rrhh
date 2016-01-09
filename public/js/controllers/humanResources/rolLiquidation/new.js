'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
    '$scope',
    '$modal',
    'server',
    '$state',
    '$window',
    '$rootScope',
    function ($scope,$modal,server,$state, $window,$rootScope) {
        $scope.rolLiquidation = {};
        $scope.rolLiquidation.departmentName = '';
        $scope.employees = [];
        $scope.employeSelections = [];
        $scope.countEmployee = 0;
        $scope.prueba = 'hola';
        $scope.paymenthroles=[];

        $rootScope.$on('employees', function (event, values) {
            $scope.employeSelections = values.employeSelections;
        });


        $scope.searchEmployeAct = function () {

            if($scope.rolLiquidation.monthSettlement) {
                server.post('getEmployees').success(function (result) {
                    $scope.employees = _(result).where({'status': 'Activo'});
                    $rootScope.$broadcast('employees', {employeSelections: $scope.employees});
                });

                $rootScope.$on('employees', function (event, values) {
                    $scope.employeSelections = values.employeSelections;
                });
            }else{
                $scope.selectedAllEmp=false;
                toastr.error('Seleccione el Mes de Liquidacion para poder seleccionar los empleados');

            }
        };


        $scope.listFechas= function(){

            $scope.rolLiquidation.firstDay='';
            $scope.rolLiquidation.lastDay='';

            $scope.date = new Date();
            $scope.anhoAct = $scope.date.getFullYear();


            if($scope.typeSettlement=='monthly'){

                //alert('Año ' + $scope.anhoAct + ' ,  Mes' + $scope.rolLiquidation.monthSettlement);
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

                //alert('Año ' + $scope.anhoAct + ' ,  Mes' + $scope.rolLiquidation.monthSettlement);
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
                $scope.paymenthroles = _(result).where({ 'monthliquidation':  $scope.mesSel });
                //console.log($scope.paymenthroles,'mes',$scope.mesSel,result,$scope.paymenthroles.length);
                if($scope.paymenthroles.length>0){
                    alert('Ya a sido hecha la liquidacion de este mes');
                    $scope.rolLiquidation.monthSettlement = '';
                    $scope.rolLiquidation.firstDay = '';
                    $scope.rolLiquidation.lastDay = '';
                }
            });
            //preguntar si esta validacion es solo con estatus liquidation o para ambas liquidation y preliquidation



        };


        handlePanelAction();
    }

]);
