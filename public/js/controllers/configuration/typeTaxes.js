'use strict';
angular.module('app').controller('TypeTaxesConfigurationCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  function ($scope, server, SweetAlert) {

    $scope.isUpdate = false;
    $scope.isServerProcess = false;
    $scope.taxType = {};
    $scope.taxTypes = [];

    var loadTaxTypes = function () {
      server.getAll('taxTypes').success(function (data) {
        $scope.taxTypes = data;
      });
    };

    var save = function(){
      server.save('taxTypes', $scope.taxType).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var update = function(){
      server.update('taxTypes', $scope.taxType, $scope.taxType._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var isParent = function(id){
      var object = _($scope.masterTaxTypes).findWhere({'parentTaxType_id': id});
      if (object != null){
        return true;
      } else {
        return false;
      }
    }


    $scope.save = function(formIsValid) {
      if(formIsValid) {
        $scope.serverProcess = true;
        if($scope.isUpdate === true){
          update();
        }else {
          save();
        }
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    }

    $scope.clean = function(){
      $scope.isUpdate = false;
      $scope.serverProcess = false;
      $scope.taxType = {};
      $scope.taxTypeForm.$setPristine();
      loadTaxTypes();
    }


    $scope.selectTaxType = function(selectedTaxType){
      $scope.isUpdate = true;
      $scope.taxType = angular.copy(selectedTaxType);
    };

    $scope.delete = function (categoryId) {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este tipo de impuesto?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('taxTypes', $scope.taxType._id).success(function(result){
              $scope.serverProcess = false;
              if(result.type == 'success') {
                SweetAlert.swal("Eliminado!", result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
              }
            })
          }
        });
    };

    loadTaxTypes();
    handlePanelAction();
  }
]);
