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


        $scope.searchEmployeAct = function () {
            server.post('getEmployees').success(function(result){
                $scope.employees = _(result).where({ 'status':  'Activo' });
            });
            $rootScope.$broadcast('employees', { employeSelections: $scope.employees });

        };

        $rootScope.$on('employees', function (event, values) {
            $scope.employeSelections = values.employeSelections;
        });


        $scope.searchSettlement = function (fecha) {

            //buscar si hay liquidaciones en el mes/quincena seleccionada y arrojar mensaje si ya fue hecha
        };

        $scope.listFechas= function(){
            //alert('Tipo ' + $scope.typeSettlement);
            $scope.rolLiquidation.firstDay='';
            $scope.rolLiquidation.lastDay='';

            $scope.date = new Date();
            $scope.anhoAct = $scope.date.getFullYear();

            if($scope.typeSettlement=='monthly'){
                $scope.mesSel = $scope.rolLiquidation.monthSettlement;

                //alert('Año ' + $scope.anhoAct + ' ,  Mes' + $scope.rolLiquidation.monthSettlement);

                var objDate1 = new Date($scope.anhoAct, $scope.mesSel - 1, 1);
                $scope.rolLiquidation.firstDay =  new Intl.DateTimeFormat().format(objDate1);
                var objDate2 = new Date($scope.anhoAct,$scope.mesSel, 0);
                $scope.rolLiquidation.lastDay  = new Intl.DateTimeFormat().format(objDate2);

            }

            //console.log('Debes imprimir', $scope.rolLiquidation.firstDay, $scope.rolLiquidation.lastDay);


        };


        handlePanelAction();
    }

]);
