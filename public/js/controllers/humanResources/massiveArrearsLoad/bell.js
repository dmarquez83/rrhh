'use strict';
angular.module('app').controller('BellCtrl', [
    '$scope',
    '$state',
    'documentValidate',
    'server',
    'SweetAlert',
    function ($scope,$state,documentValidate, server, SweetAlert) {
        $scope.serverProcess = false;
        $scope.isUpdate = false;
        $scope.bells = [];
        $scope.bells_for_save = [];
        $scope._id = [];
        $scope.countBell =[];
        $scope.hourBell= [];
        $scope.typeBell= [];


        server.post('getBells').success(function(result){

            $.each(result, function( index, bell ) {
              result[index].hourBell = new Date(1970, 0, 1, parseInt(bell.hourBell.substring(0,2)), parseInt(bell.hourBell.substring(3,5)), 0);
            });

            $scope.bells = result;

            if($scope.bells.length==0){
                $scope.addBell();
            }
        });


        $scope.addBell = function() {
            $scope.bells.push({ hourBell: new Date(1970, 0, 1, 0, 0, 0)});
        };

        $scope.deleteBell = function(index, bell){
            $scope.bells.splice(index,1);
            if($scope.bells.length==0){
                $scope.addBell();
            }            
        };

        $scope.save= function () {

            angular.copy($scope.bells, $scope.bells_for_save);

            $.each($scope.bells_for_save, function( index, bell ) {
              $scope.bells_for_save[index].hourBell = bell.hourBell.toTimeString("HH:mm").substring(0,5);
            });

            server.save('bells',$scope.bells_for_save).success(function (data) {
                toastr[data.type](data.msg);
            });
        };

        $scope.timeValidate= function (hora) {
            //alert('Validate'+ hora + 'largo: ' + hora.length);

            var a= hora.charAt(0);
            var b= hora.charAt(1);
            var c= hora.charAt(2);
            var d= hora.charAt(3);
            var e= hora.charAt(4);

            var partHour = parseInt(a +''+ b);
            var partMinutes = parseInt(d + '' + e);

            //alert ('HORA ' + partHour + 'Minutos ' + partMinutes + 'Separador ' + c);

            if ((partHour>='0') && (partHour<='23')) return true;
            else{
                toastr.warning('La hora debe estar comprendida entre 00 y 23');
                return false;
            }

            if (c==':') return true;
            else{
                toastr.warning('El separador debe ser : ');
                return false;
            }

            if ((partMinutes>='0') && (partMinutes<='59')) return true;
            else{
                toastr.warning('Los minutos deben estar comprendidos entre 00 y 59');
                return false;
            }
        }










        handlePanelAction();
    }
]);