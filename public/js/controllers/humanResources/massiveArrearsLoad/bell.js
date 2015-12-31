'use strict';
angular.module('app').controller('BellCtrl', [
    '$scope',
    'documentValidate',
    'server',
    'SweetAlert',
    function ($scope, documentValidate, server, SweetAlert) {
        $scope.serverProcess = false;
        $scope.isUpdate = false;
        $scope.bell = {};
        $scope._id = null;
        $scope.countBell = [];
        $scope.hourBell.value = [];
        $scope.typeBell= [];

        $scope.addBell = function() {
            $scope.bell.push({ hecho: true });
        };

        $scope.deleteBell = function(index){
            $scope.bell.splice(index, 1);
        };

        var getBells= function () {
            server.getAll('scheduleConfiguration').success(function (data) {
                $scope.bell = data;
            });
        };

        var editBell = function(selectedBell){
            $scope.isUpdate = true;
            $scope.bell = selectedBell;
            $scope.$digest();
        };

        var save = function(){
            $scope.serverProcess = true;
            server.save('scheduleConfiguration', $scope.bell).success(function (result) {
                $scope.serverProcess = false;
                toastr[result.type](result.msg);
                if(result.type == 'success'){
                    $scope.cleanBell();
                }
            });
        }

        $scope.save = function (formIsValid) {
            if(formIsValid){
                if ($scope.isUpdate) {
                    update();
                } else {
                    save();
                }
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
                        server.delete('scheduleConfiguration', $scope.bell._id).success(function(result){
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