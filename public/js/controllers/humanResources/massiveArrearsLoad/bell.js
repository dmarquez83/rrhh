'use strict';
angular.module('app').controller('BellCtrl', [
    '$scope',
    'documentValidate',
    'server',
    'SweetAlert',
    function ($scope, documentValidate, server, SweetAlert) {
        $scope.serverProcess = false;
        $scope.isUpdate = false;
        $scope.bells = [];
        $scope._id = null;
        $scope.countBell =[];
        $scope.hourBell= [];
        $scope.typeBell= [];
        $scope.bellInfo = [];


        $scope.addBell = function() {
            $scope.bells.push({ hecho: true });
            console.log($scope.bells,'agregarasss');
        };

        $scope.deleteBell = function(index){
            $scope.bells.splice(index, 1);
        };

       server.post('getBells').success(function(result){
            $scope.bells = (result);
           var indice =0;
           angular.forEach(($scope.bells), function(row){

               console.log(row,'este',row.countBellindice);
               $scope.countBell[indice] = row.countBell;
               $scope.hourBell[indice] = row.hourBell;
               $scope.typeBell[indice] = row.typeBell;
               indice++;

           });
        });



        var editBell = function(selectedBell){
            $scope.isUpdate = true;
            $scope.bells = selectedBell;
            $scope.$digest();
        };

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

        $scope.save = function () {
            var index = 0;
            $scope.bellInfo_=[];
            $scope.bellInfo = {};
            $scope.bellInfo.countBell = '';
            $scope.bellInfo.hourBell = '';
            $scope.bellInfo.typeBell = '';
            angular.forEach($scope.countBell, function () {
                $scope.bellInfo.countBell =  $scope.countBell[index];
                $scope.bellInfo.hourBell  =  $scope.hourBell[index];
                $scope.bellInfo.typeBell  =  $scope.typeBell[index];
                $scope.bellInfo_.push($scope.bellInfo);

                console.log($scope.bellInfo,'revisa lo que tienes aqui');
                console.log($scope.bellInfo_,'arreglo');

                $scope.bellInfo = {};

            index++;
            } );

            server.save('bells',$scope.bellInfo_).success(function (data) {

            });
        };


        /*$scope.save = function(){
            toastr.warning('Debo guardar');
            if ($scope._id == null) { //guardar
                $scope.bell.push({
                    countBell: $scope.countBell,
                    hourBell: $scope.hourBell.value,
                    typeBell: $scope.typeBell
                });
            } else { //editar
                $scope.bell[$scope._id] = {
                    countBell: $scope.countBell,
                    hourBell: $scope.hourBell.value,
                    typeBell: $scope.typeBell
                };
            }
            $scope.cleanBell();
        };*/

        $scope.cleanBell = function() {
            $scope._id = null;
            $scope.countBell = [];
            $scope.hourBell.value = [];
            $scope.typeBell= [];
        };


        $scope.delete = function () {
            SweetAlert.swal({
                    title: "Está seguro de eliminar este timbre?",
                    text: "Si elimina este registro no lo podrá recuperar",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
                    cancelButtonText: "No, cancelar",
                    closeOnConfirm: true,
                    closeOnCancel: true },
                function(isConfirm){
                    if (isConfirm) {
                        $scope.serverProcess = true;
                        server.delete('scheduleConfiguration', $scope.bells._id).success(function(result){
                            if(result.type == 'success') {
                                $scope.serverProcess = false;
                                SweetAlert.swal("Timbre Eliminado!", result.msg, result.type);
                                $scope.cleanBell();
                            } else {
                                SweetAlert.swal("Error al eliminar timbre!", result.msg, result.type);
                                $scope.cleanBell();
                            }
                        })
                    }
                });
        };


        handlePanelAction();
    }
]);