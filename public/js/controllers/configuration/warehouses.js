'use strict';
angular.module('app').controller('WarehouseCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'SweetAlert',
  function ($scope, documentValidate, server, SweetAlert) {
    $scope.newWarehouse = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getWarehouses = function () {
      server.getAll('warehouse').success(function (data) {
        $scope.warehouses = data;
      });
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('warehouse', $scope.newWarehouse, $scope.newWarehouse._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('warehouse', $scope.newWarehouse).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
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
        toastr.warning("Revisar errores en el formulario");
      }
    };

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.newWarehouse = {};
      $scope.warehouseForm.$setPristine();
      getWarehouses();

    };

    $scope.selectWarehouse = function (selectedWarehouse) {
      $scope.newWarehouse = angular.copy(selectedWarehouse);
      $scope.isUpdate = true;
    };

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar esta bodega?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('warehouse', $scope.newWarehouse._id).success(function(result){
              if(result.type == 'success') {
                $scope.serverProcess = false;
                SweetAlert.swal("Eliminado!", result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
                $scope.clean();
              }
            })
          }
        });
    };

    getWarehouses();
    handlePanelAction();
  }
]);
