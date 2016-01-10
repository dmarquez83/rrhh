'use strict';
angular.module('app').controller('RolPagoEmployeeCtrl', [
    '$scope',
    '$modalInstance',
    'server',
    '$rootScope',
    'EmployeSelectionsModal',
    'monthSettlement',
    'typeSettlement',
    function ($scope, $modalInstance, server, $rootScope,EmployeSelectionsModal,monthSettlement,typeSettlement) {
        $scope.employee = EmployeSelectionsModal;
        $scope.monthSettlementModal=monthSettlement;
        $scope.typeSettlementModal=typeSettlement;
        $scope.less = 9.35;

        if ($scope.typeSettlementModal=='monthly'){
            $scope.tipo = 'Mensual';
            $scope.mesSel = $scope.monthSettlementModal;
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
            $scope.mesSel = $scope.monthSettlementModal;
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


        $scope.cancel = function () {
            $modalInstance.dismiss();
        }

        handlePanelAction();
    }

]);
