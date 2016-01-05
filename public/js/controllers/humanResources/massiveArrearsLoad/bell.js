'use strict';
angular.module('app').controller('BellCtrl', [
    '$scope',
    'documentValidate',
    'server',
    'SweetAlert',
    function ($scope, documentValidate, server, SweetAlert) {
        $scope.serverProcess = false;
        $scope.isUpdate = false;
        $scope.bells = {};
        $scope._id = null;
        $scope.countBell = '';
        $scope.hourBell= '';
        $scope.typeBell= '';

        $scope.addBell = function() {
            $scope.bells.push({ hecho: true });
        };

        $scope.deleteBell = function(index){
            $scope.bells.splice(index, 1);
        };

        var getBells= function () {
            server.getAll('bells').success(function (data) {
                $scope.bells = data;
            });
        };

        //getBells();   //cuando descomento esto da error y me dice que no existe localhost:8000/bells..revisar la ruta

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
                toastr.warning('Seleccione el tip');
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

        $scope.save = function (formIsValid) {
            if (validate() && formIsValid) {
                $scope.serverProcess = true;
                server.save('bells', $scope.bells).success(function (data) {
                    $scope.serverProcess = false;
                    toastr[data.type](data.msg);
                    if (data.type == 'success') {
                        $scope.clean();
                    }
                });
            } else {
                toastr.warning("Debe ingresar todos los datos");
            }
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