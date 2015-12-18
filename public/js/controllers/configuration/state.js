'use strict';
angular.module('app').controller('StateCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'SweetAlert',
  function ($scope, documentValidate, server, SweetAlert) {
    $scope.allDocuments = {documentsConfiguration:[]};
    $scope.state = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;
    var getStates = function () {
      server.getAll('state').success(function (data) {
        $scope.states = data;
      });
    };
    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.state = {};
      $scope.stateForm.$setPristine();
      getStates();
    };
    server.getAll('documentsConfiguration').success(function (data) {
      $scope.allDocuments.documentsConfiguration = data;
    });
    $scope.save = function (formIsValid) {
      if(formIsValid) {
        $scope.serverProcess = true;
        if ($scope.isUpdate) {
          server.update('state', $scope.state, $scope.state._id).success(function (data) {
            $scope.serverProcess = false;
            toastr[data.type](data.msg);
            if (data.type == 'success') {
              $scope.clean();
            }
          });
          getStates();
        } else {
          server.save('state', $scope.state).success(function (data) {
            $scope.serverProcess = false;
            toastr[data.type](data.msg);
            if (data.type == 'success') {
              $scope.clean();
            }
          });
          getStates();
        }
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    };
    $scope.deleteState = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este estado?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('state', $scope.state._id).success(function(result){
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
    $scope.configSelectedDocuments = {
      create: false,
      valueField: '_id',
      labelField: 'name',
      render: {
        item: function (item, escape) {
          return '<div>' + item.name + '</div>';
        },
        option: function (item, escape) {
          return '<div>' + '<h6>' + item.code + ' - ' + item.name + '</h6>' + '</div>';
        }
      },
      searchField: [
        'code',
        'name'
      ],
      placeholder: 'Seleccione documentos'
    };
    $scope.selectState = function (selectedState) {
      $scope.state = selectedState;
      $scope.isUpdate = true;
    };
    getStates();
    handlePanelAction();
  }
]);
