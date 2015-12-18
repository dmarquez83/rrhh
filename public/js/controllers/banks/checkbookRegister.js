'use strict';
angular.module('app').controller('CheckbookRegisterCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'SweetAlert',
  function ($scope, documentValidate, server, SweetAlert) {
    $scope.newBankAccount = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getCheckbookRegisters = function () {
      server.getAll('checkbookRegister').success(function (data) {
        $scope.checkbookRegisters = data;
      });
    };

    var getBankAccounts = function () {
      server.getAll('bankAccount').success(function (data) {
        $scope.bankAccounts = data;
      });
    };

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.newCheckbookRegister = {};
      getCheckbookRegisters();
      $scope.checkbookRegisterForm.$setPristine();
    };

    server.getAll('statement').success(function (data) {
      $scope.assets = _(data).filter(function (ledgerAccounts) {
        if (ledgerAccounts.parent == 'Bancos') {
          return true;
        }
        ;
      });
    });

    var update = function(){
      $scope.serverProcess = true;
      server.update('checkbookRegister', $scope.newCheckbookRegister, $scope.newCheckbookRegister._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('checkbookRegister', $scope.newCheckbookRegister).success(function (result) {
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

    $scope.selectCheckbookRegister = function (selectedCheckbookRegister) {
      $scope.newCheckbookRegister = selectedCheckbookRegister;
      $scope.isUpdate = true;
    };

    $scope.delete = function (index) {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar esta chequera?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('checkbookRegister', $scope.checkbookRegisters[index]._id).success(function(result){
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

    getBankAccounts();
    getCheckbookRegisters();
    handlePanelAction();
  }
]);