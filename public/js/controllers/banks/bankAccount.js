'use strict';
angular.module('app').controller('BankAccountCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'SweetAlert',
  function ($scope, documentValidate, server, SweetAlert) {
    $scope.newBankAccount = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getBankAccounts = function () {
      server.getAll('bankAccount').success(function (data) {
        $scope.bankAccounts = data;
      });
    };

    var getBanks = function () {
      server.getAll('bank').success(function (data) {
        $scope.banks = data;
      });
    };

    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.newBankAccount = {};
      getBankAccounts();
      $scope.bankAccountForm.$setPristine();
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
      server.update('bankAccount', $scope.newBankAccount, $scope.newBankAccount._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('bankAccount', $scope.newBankAccount).success(function (result) {
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

    $scope.selectBankAccount = function (selectedBankAccount) {
      $scope.newBankAccount = selectedBankAccount;
      $scope.isUpdate = true;
    };

    $scope.getBankName = function () {
      $scope.newBankAccount.bank_id = _($scope.banks).findWhere({ 'code': $scope.newBankAccount.bank_code }).name;
    };

    $scope.delete = function (index) {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar esta cuenta bancaria?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('bankAccount', $scope.bankAccounts[index]._id).success(function(result){
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

    getBanks();
    getBankAccounts();
    handlePanelAction();
  }
]);