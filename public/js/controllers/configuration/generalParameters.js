'use strict';
angular.module('app').controller('GeneralParametersCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {
    $scope.newParemeter = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;
    var getParemeters = function () {
      server.getAll('generalParameters').success(function (data) {
        $scope.paremeters = data;
      });
    };
    $scope.save = function () {
      $scope.serverProcess = true;
      if ($scope.isUpdate) {
        server.update('generalParameters', $scope.newParemeter, $scope.newParemeter._id).success(function (data) {
          $scope.serverProcess = false;
          $scope.isUpdate = false;
          getParemeters();
          $scope.newParemeter = {};
          toastr[data.type](data.msg);
        });
      } else {
        server.save('generalParameters', $scope.newParemeter).success(function (data) {
          $scope.serverProcess = false;
          getParemeters();
          $scope.newParemeter = {};
          toastr[data.type](data.msg);
        });
      }
    };
    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.newParemeter = {};
    };
    $scope.selectParemeter = function (selectedParemeter) {
      $scope.newParemeter = angular.copy(selectedParemeter);
      $scope.isUpdate = true;
    };
    $scope.deleteparemeter = function (index) {
      var paremeter = $scope.paremeters[index];
      alertify.confirm('\xbfEst\xe1 seguro de eliminar este parametro?').set('title', 'Confirmar').set('labels', {
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).set('onok', function (closeEvent) {
        server.delete('generalParameters', paremeter._id).success(function (data) {
          if (data) {
            toastr[data.type](data.msg);
            $scope.newParemeter = {};
            getParemeters();
            $scope.isUpdate = false;
          }
        });
      }).show();
    };
    getParemeters();
    handlePanelAction();
  }
]);