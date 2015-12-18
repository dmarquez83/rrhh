'use strict';
angular.module('app').controller('BankCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'SweetAlert',
  function ($scope, documentValidate, server, SweetAlert) {
    $scope.newBank = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getBanks = function () {
      server.getAll('bank').success(function (data) {
        $scope.banks = data;
      });
    };

    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.newBank = {};
      getBanks();
      $scope.bankForm.$setPristine();
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('bank', $scope.newBank, $scope.newBank._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('bank', $scope.newBank).success(function (result) {
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

    $scope.delete = function (index) {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este banco?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('bank', $scope.banks[index]._id).success(function(result){
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

    $scope.selectBank = function (selectedBank) {
      $scope.newBank = selectedBank;
      $scope.isUpdate = true;
    };

    getBanks();
    handlePanelAction();
  }
]);