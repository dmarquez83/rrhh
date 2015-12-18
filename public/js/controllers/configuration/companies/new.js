'use strict';
angular.module('app').controller('NewCompanyCtrl', [
  '$scope',
  'server',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, server, optionsDataTable, DTOptionsBuilder) {

    $scope.loading = 'hide';
    $scope.newCompany = {
      creationDate : moment().format('YYYY-MM-DD HH:mm:ss')
    };
    $scope.isUpdate = false;
    $scope.companies = [];
    var fileName = 'resumen_companies';
    var title = 'Resumen Empresas';
    var tableInstance = null;

    var selectedCompany = function(company){
      $scope.isUpdate = true;
      $scope.newCompany = angular.copy(company);
      $scope.$digest();
    };

    $scope.save = function(formIsValid){
      if (formIsValid) {
        $scope.loading = '';
        server.save('companies', $scope.newCompany).success(function(result){
          $scope.loading = 'hide';
          toastr[result.type](result.msg);
          if(result.type == 'success'){
            tableInstance.reloadData();
            $scope.clean();
          }
        });
      } else {
        toastr.warning('Revise errores en el formulario');
      }
    };

    $scope.deleteCompany = function(){

    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'creationDate', title: 'Fecha de Creación'},
      {field: 'code', title: 'Código'},
      {field: 'name', title: 'Nombre'},
      {field: 'databaseName', title: 'Base de datos'},
    ]);

    $scope.dtOptions = DTOptionsBuilder
    .fromSource(optionsDataTable.fromSource('companies/forTable'))
    .withDataProp('data')
    .withOption('serverSide', true)
    .withOption('rowCallback', optionsDataTable.rowCallback(selectedCompany))
    .withOption('order', [0, 'desc'])
    .withOption('iDisplayLength', 25)
    .withOption('deferRender', true)
    .withOption('scrollY', optionsDataTable.scrollY60)
    .withOption('dom', optionsDataTable.dom)
    .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };


    $scope.clean = function(){
      $scope.newCompany = {creationDate : moment().format('YYYY-MM-DD HH:mm:ss')};
      $scope.isUpdate = false;
      $scope.loading = 'hide';
    };

    handlePanelAction();


  }
]);
