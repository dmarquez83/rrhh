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


        $scope.addBell = function() {
            $scope.bells.push({ hecho: true });
            //console.log($scope.bells,'agregara linea');
        };

        $scope.deleteBell = function(index){
            console.log($scope);
            $scope.bells.splice(index,1);
            //console.log('voy a quitar linea', index);

            var idBell = $scope._id[index];

            /*console.log('bells',  $scope.bells[index]);
            console.log('idBell es', idBell);*/

            if (idBell){
                eliminar(idBell);
                console.log('borre ', idBell);
            }
        };

        var eliminar = function ($id) {
            server.delete('bells',$id).success(function(result){
                if(result.type == 'success') {
                    toastr[data.type](data.msg);
                    //console.log('borrado es ', $id);
                    //$state.reload();
                }
            });
        };

        $scope.cleanBell = function() {
            $scope._id = null;
            $scope.countBell = [];
            $scope.hourBell= [];
            $scope.typeBell= [];
        };


        server.post('getBells').success(function(result){
                $scope.bells = (result);
                var indice =0;
                angular.forEach(($scope.bells), function(row){
                    console.log(row,'este',row._id);
                    $scope._id[indice] = row._id;
                    $scope.countBell[indice] = row.countBell;
                    $scope.hourBell[indice] = row.hourBell;
                    $scope.typeBell[indice] = row.typeBell;
                    indice++;
                });

                $scope.cuenta = $scope.bells.length;
        });

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

                console.log($scope.bellInfo_,'arreglo');

                $scope.bellInfo = {};

                index++;
            } );

            server.save('bells',$scope.bellInfo_).success(function (data) {
                toastr[data.type](data.msg);
            });
        };


        var eliminar = function ($id) {
            server.delete('bells',$id).success(function(result){
                        if(result.type == 'success') {
                            toastr[data.type](data.msg);
                            //console.log('borrado es ', $id);
                            $state.reload();
                        }
                    });
        };

        $scope.cleanBell = function() {
            $scope._id = null;
            $scope.countBell = [];
            $scope.hourBell= [];
            $scope.typeBell= [];
        };

        handlePanelAction();
    }
]);