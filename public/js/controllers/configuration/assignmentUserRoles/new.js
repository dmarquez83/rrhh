'use strict';
angular.module('app').controller('NewAssignmentUserRolesCtrl', [
  '$scope',
  'server',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, server, optionsDataTable, DTOptionsBuilder) {
    $scope.roles = [];
    $scope.user = {};
    $scope.user.role_id = '';
    $scope.user.warehouses = [];
    $scope.user.isEnabled = true;
    $scope.user.default_warehouse_id = '';
    $scope.selectedWarehouses= [];
    $scope.warehouses = [];
    $scope.isUpdate = false;
    var fileName = "resumen_usuarios";
    var title = "Resumen Usuarios";
    var tableInstance = null;

    var getRoles = function() {
      server.getAll("roles").success(function (result) {
        $scope.roles = _(result).filter(function(rol){
          if(rol.name != 'root'){
            return rol;
          }
        });
      });
    }

    var getWarehouses = function() {
      server.getAll("warehouse").success(function (result) {
        $scope.warehouses = result;
      });
    }

    var getUsers = function() {
      server.getAll("users").success(function (result) {
        $scope.users = result;
      });
    }

    var getEmployees = function(){
      server.getAll("employee").success(function(result){
        $scope.employees = result;
      });
    }

    var validateData = function() {
      if ($scope.user.role_id == ''){
        toastr.warning("Porfavor asigne un rol al usuario");
        return false;
      }
      if ($scope.user.configuration[0].selected_warehouses.length == 0){
        toastr.warning("Selecciona por lo menos una bodega");
        return false;
      } else {
        if($scope.user.configuration[0].default_warehouse_id == ''){
          toastr.warning("Selecciona una bodega por defecto");
          return false;
        }
      }
      return true;
    }

    $scope.configSelectRoles = {
      create: false,
      valueField: '_id',
      labelField: 'name',
      render: {
        item: function (item, escape) {
          return '<div>' + item.name + '</div>';
        },
        option: function (item, escape) {
          return '<div>' + '<h5>' + item.name + '</h5>' + '</div>';
        }
      },
      searchField: [
        'name',
      ],
      placeholder: 'Seleccione un rol',
      maxItems: 1,
      hideSelected: true
    }

    $scope.configSelectWarehouses = {
      create: false,
      valueField: '_id',
      labelField: 'name',
      preload: true,
      render: {
        item: function (item, escape) {
          return '<div>' + item.name + '</div>';
        },
        option: function (item, escape) {
          return '<div>' + '<h6>' + item.name+ '</h6>' + '</div>';
        }
      },
      searchField: [
        'name'
      ],
      placeholder: 'Seleccionar Bodegas',
      onItemAdd: function(item){
          var warehouse = angular.copy(_($scope.warehouses).findWhere({'_id': item}));
          $scope.selectedWarehouses.push(angular.copy(warehouse));
          $scope.selectedWarehouses = _($scope.selectedWarehouses).uniq();

      }
    }

    $scope.configSelectWarehouse = angular.copy($scope.configSelectWarehouses);
    $scope.configSelectWarehouse.placeholder = 'Seleccionar Bodega Por Defecto';
    $scope.configSelectWarehouse.onItemAdd = null;
    $scope.configSelectWarehouse.maxItems = 1;

    $scope.configSelectEmployees = {
      create: false,
      valueField: '_id',
      labelField: 'names',
      render: {
        item: function (item, escape) {
          return '<div>' + item.names + ' ' + item.surnames + '</div>';
        },
        option: function (item, escape) {
          return '<div>' + '<h5>' + item.names + ' ' + item.surnames + ' ' + '<small>' + escape(item.identification) + '</small>' + '</h5>' + '</div>';
        }
      },
      searchField: [
        'identification',
        'names',
        'surnames'
      ],
      placeholder: 'Seleccione empleado',
      maxItems: 1,
      hideSelected: true
    };

    $scope.save = function () {
      if(validateData()) {
        if ($scope.isUpdate) {
          server.update("users", $scope.user, $scope.user._id).success(function (data) {
            toastr[data.type](data.msg);
            if(data.type == 'success') {
              $scope.clean();
            }
          });
        } else {
          server.save("users", $scope.user).success(function (data) {
            toastr[data.type](data.msg);
            if(data.type == 'success') {
              $scope.clean();
            }
          });
        }
      }
    };

    var updateSelections = function(){
      $scope.selectedWarehouses = [];
      var selectedWarehouses = angular.copy($scope.user.configuration[0].selected_warehouses);
      _(selectedWarehouses).each(function(warehouseId){
        var warehouse = angular.copy(_($scope.warehouses).findWhere({'_id': warehouseId}));
        $scope.selectedWarehouses.push(angular.copy(warehouse));
      });
    }

    var selectUser = function(user){
      $scope.isUpdate = true;
      var selectedUser = angular.copy(user);
      $scope.user = _(selectedUser).omit('password');
      $scope.$digest();
    };

    $scope.deleteUser = function (user) {
      alertify.confirm('\xbfEst\xe1 seguro de eliminar este usuario?').set('title', 'Confirmar').set('labels', {
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).set('onok', function () {
        server.delete("users", user._id).success(function (data) {
          toastr[data.type](data.msg);
          $scope.clean();
        });
      }).show();
    };

    $scope.clean = function(){
      $scope.roles = [];
      $scope.user = {};
      $scope.user.role_id = '';
      $scope.user.warehouses = [];
      $scope.user.isEnabled = true;
      $scope.user.default_warehouse_id = '';
      $scope.selectedWarehouses = [];
      $scope.warehouses = [];
      $scope.isUpdate = false;
      getRoles();
      getWarehouses();
      getUsers();
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'creationDate', title: 'Fecha de Creación'},
      {field: 'email', title: 'Correo'},
      {field: 'username', title: 'Usuario'},
      {field: 'isEnabled', title: 'Habilitado'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
    .fromSource(optionsDataTable.fromSource('users/forTable'))
    .withDataProp('data')
    .withOption('serverSide', true)
    .withOption('rowCallback', optionsDataTable.rowCallback(selectUser))
    .withOption('order', [0, 'desc'])
    .withOption('iDisplayLength', 25)
    .withOption('deferRender', true)
    .withOption('scrollY', optionsDataTable.scrollY60)
    .withOption('dom', optionsDataTable.dom)
    .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    getRoles();
    getWarehouses();
    getUsers();
    getEmployees();

    handlePanelAction();
  }
]);
