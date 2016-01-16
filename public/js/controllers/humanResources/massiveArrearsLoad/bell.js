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
        $scope._id = [];
        $scope.countBell =[];
        $scope.hourBell= [];
        $scope.typeBell= [];
        $scope.prueba=false;


        $scope.addBell = function() {
            $scope.bells.push({ hecho: true });
        };

        $scope.deleteBell = function(index){
            $scope.prueba=true;
            $scope.bells.splice(index,1);
            var indice =0;
            angular.forEach(($scope.bells), function(row){
                $scope._id[indice] = row._id;
                $scope.countBell[indice] = row.countBell;
                $scope.hourBell[indice] = row.hourBell;
                $scope.typeBell[indice] = row.typeBell;
                indice++;
            });
            var idBell = $scope._id[index];
            if (idBell){
                eliminar(idBell);
            }
        };

        var eliminar = function ($id) {
            server.delete('bells',$id).success(function(result){
                if(result.type == 'success') {
                    toastr[result.type](result.msg);
                }
            });
        };

        $scope.cleanBell = function() {
            $scope._id = null;
            $scope.countBell = [];
            $scope.hourBell= [];
            $scope.typeBell= [];
        };

        if(!$scope.prueba){
            server.post('getBells').success(function(result){
                $scope.bells = (result);
                var indice =0;
                angular.forEach(($scope.bells), function(row){
                    $scope._id[indice] = row._id;
                    $scope.countBell[indice] = row.countBell;
                    $scope.hourBell[indice] = row.hourBell;
                    $scope.typeBell[indice] = row.typeBell;
                    indice++;
                });

                $scope.cuenta = $scope.bells.length;
            });
        }


        var validateCountBell = function(){
            if ($scope.countBell.length == 0){
                toastr.warning('Debe ingresar el contador');
                return false;
            }
            return true;
        };

        var validateHourBell = function(){
            if ($scope.hourBell.length == 0){
                toastr.warning('Ingrese la hora');
                return false;
            }
            return true;
        };

        var validateTypeBell = function(){
            if ($scope.typeBell.length == 0){
                toastr.warning('Seleccione el tipo');
                return false;
            }
            return true;
        };

        var validate = function(){
            if (validateCountBell() && validateHourBell() && validateTypeBell()){
                return true;
            }
            return false;
        };

        $scope.save= function () {
            $scope.bellInfo_=[];
            $scope.bellInfo = {};
            $scope.bellInfo.countBell = '';
            $scope.bellInfo.hourBell = '';
            $scope.bellInfo.typeBell = '';

            var index = 0;
            angular.forEach($scope.countBell, function () {
                $scope.bellInfo.countBell =  parseInt($scope.countBell[index]);
                $scope.bellInfo.hourBell  =  $scope.hourBell[index];
                $scope.bellInfo.typeBell  =  $scope.typeBell[index];
                $scope.bellInfo_.push($scope.bellInfo);
                $scope.bellInfo = {};
                index++;
            } );

            server.save('bells',$scope.bellInfo_).success(function (data) {
                toastr[data.type](data.msg);
            });
        };

        handlePanelAction();
    }
]);