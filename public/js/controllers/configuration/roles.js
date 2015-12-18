'use strict';
angular.module('app').controller('RolesCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  function ($scope, server, SweetAlert) {
    $scope.isUpdate = false;
    $scope.newRole = {};
    $scope.roles = [];

    var getRoles = function(){
      server.getAll('roles').success(function(result){
        $scope.roles = result;
      });
    };

    var getModules = function(){
      server.getAll('modules').success(function(result){
        $scope.newRole.modules = _(result).sortBy('order');
      });
    };

    $scope.save = function(){
      if($scope.isUpdate){
        server.update('roles', $scope.newRole, $scope.newRole._id).success(function(result){
          toastr[result.type](result.msg);
          console.log(result);
          if(result.type == 'success'){
            $scope.clean();
          }
        });
      } else {
        server.save('roles', $scope.newRole).success(function(result){
          toastr[result.type](result.msg);
          if(result.type == 'success'){
            $scope.clean();
          }
        });
      }
    };

    $scope.delete = function(index){
      var id = $scope.roles[index]._id;
      SweetAlert.swal({
          title: "Esta seguro?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('roles', id).success(function(result){
              $scope.clean();
              if(result.type == 'success') {
                SweetAlert.swal("Eliminado!", result.msg, result.type);
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
              }
            })
          }
        });

    };

    $scope.selectRole = function(selectedRole){
      $scope.isUpdate = true;
      $scope.newRole = angular.copy(selectedRole);
      $scope.modules = angular.copy(selectedRole.modules);
    };

    $scope.clean = function(){
      $scope.isUpdate = false;
      $scope.newRole = {};
      $scope.roles = [];
      getModules();
      getRoles();
    };


    getModules();
    getRoles();

    handlePanelAction();
  }
]);