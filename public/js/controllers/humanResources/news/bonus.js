'use strict';
angular.module('app').controller('BonusCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  function ($scope, server, SweetAlert) {
    $scope.bond = {};
    $scope.bond.type = 'Valor';
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getBonus = function () {
      server.getAll('bonus').success(function (data) {
        $scope.bonus = data;
      });
    };

    $scope.selectBond = function (selectedBond) {
      $scope.bond = selectedBond;
      $scope.isUpdate = true;
    };

    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.bond = {};
      $scope.bond.type = 'Valor';
      getBonus();
      $scope.bonusForm.$setPristine();
    };

    $scope.validateTypeBond = function () {
      if ($scope.bond.type == 'Porcentaje') {
        if ($scope.bond.value > 100) {
          $scope.bond.value = 100;
        }
      }
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('bonus', $scope.bond, $scope.bond._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('bonus', $scope.bond).success(function (result) {
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
          title: "Está seguro de eliminar este bono?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('bonus', $scope.bonus[index]._id).success(function(result){
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


    getBonus();
    handlePanelAction();
  }
]);