'use strict';
angular.module('app').controller('NewUserCtrl', [
  '$scope',
  'server',
  'optionsDataTable',
  'DTOptionsBuilder',
  'SweetAlert',
  function ($scope, server, optionsDataTable, DTOptionsBuilder, SweetAlert) {

    $scope.newUser = {};
    $scope.newUser.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.newUser.companies = [];
    $scope.newUser.configuration = [];
    $scope.newUser.isEnabled = true;
    $scope.newUser.default_company_id = '';
    $scope.selectedCompanies = [];
    $scope.companies = [];
    $scope.isUpdate = false;
    var fileName = "resumen_usuarios";
    var title = "Resumen Usuarios";
    var tableInstance = null;

    var getCompanies = function() {
      server.getAll("companies").success(function (result) {
        $scope.companies = result;
      });
    };

    $scope.configSelectCompanies = {
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
      placeholder: 'Seleccionar Empresas',
      onItemAdd: function(companyId){
        var company = angular.copy(_($scope.companies).findWhere({'_id': companyId}));
        var findCompany = _($scope.newUser.configuration).findWhere({'company_id': companyId});

        if(!findCompany) {
          var newUserSelectedCompany = {
            'company_id': company._id,
            'role_id': '',
            'employee_id': '',
            'selected_warehouses': [],
            'default_warehouse_id': ''
          }
          $scope.newUser.configuration.push(angular.copy(newUserSelectedCompany));
        }

      },
      onItemRemove: function(companyId){
        var findCompany = _($scope.newUser.configuration).findWhere({'company_id': companyId});
        $scope.newUser.configuration = _($scope.newUser.configuration).without(findCompany);
      }
    }

    $scope.configSelectCompany = angular.copy($scope.configSelectCompanies);
    $scope.configSelectCompany.placeholder = 'Seleccionar Empresa Por Defecto';
    $scope.configSelectCompany.onItemAdd = null;
    $scope.configSelectCompany.maxItems = 1;

    $scope.$watchCollection('newUser.companies', function() {
      $scope.selectedCompanies = [];
      _($scope.newUser.companies).each(function(companyId) {
        var company = angular.copy(_($scope.companies).findWhere({'_id': companyId}));
        $scope.selectedCompanies.push(angular.copy(company));
      });
    });

    var save = function() {
      server.save("users", $scope.newUser).success(function (data) {
        toastr[data.type](data.msg);
        if(data.type == 'success') {
          $scope.clean();
        }
      });
    };

    var update = function() {
      server.update("users", $scope.newUser, $scope.newUser._id).success(function (data) {
        toastr[data.type](data.msg);
        if(data.type == 'success') {
          $scope.clean();
        }
      });
    };

    $scope.submit = function (formIsValid) {
      if(formIsValid) {
        if ($scope.isUpdate) {
          update();
        } else {
          save();
        }
      } else {
        toastr.warning('Revise errores en el formulario');
      }
    };

    var updateSelections = function(){
      $scope.selectedCompanies = [];
      _($scope.newUser.companies).each(function(companyId){
        var company = angular.copy(_($scope.companies).findWhere({'_id': companyId}));
        $scope.selectedCompanies.push(angular.copy(company));

        var findCompany = _($scope.newUser.configuration).findWhere({'company_id': companyId});
        if(!findCompany){
          var newUserSelectedCompany = {
            'company_id': company._id,
            'role_id': '',
            'employee_id': '',
            'selected_warehouses': [],
            'default_warehouse_id': ''
          }
          $scope.newUser.configuration.push(angular.copy(newUserSelectedCompany));
        }
      });
    }

    var selectUser = function(user){
      $scope.isUpdate = true;
      var selectedUser = angular.copy(user);
      $scope.newUser = _(selectedUser).omit('password');
      $scope.$digest();
    };

    $scope.delete = function (user) {
      SweetAlert.swal({
          title: "Está seguro de eliminar este usuario?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete("users", $scope.newUser._id).success(function (result) {
              $scope.serverProcess = false;
              if(result.type == 'success') {
                SweetAlert.swal("Eliminado!", result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
              }
            });
          }
        });
      };

    $scope.clean = function(){
      $scope.newUser = {};
      $scope.newUser.companies = [];
      $scope.newUser.configuration = [];
      $scope.newUser.isEnabled = true;
      $scope.newUser.default_company_id = '';
      $scope.selectedCompanies = [];
      $scope.companies = [];
      $scope.isUpdate = false;
      tableInstance.reloadData();
      $scope.newUserForm.$setPristine();
      getCompanies();
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

    getCompanies();

    handlePanelAction();
  }
]);
