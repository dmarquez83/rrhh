'use strict';
angular.module('app').controller('OfficeCtrl', [
  '$scope',
  '$filter',
  'server',
  'SweetAlert',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  function ($scope, $filter, server, SweetAlert, optionsDataTable, dataTablePDFMaker,
            dataTableXLSXMaker, DTOptionsBuilder,DTColumnBuilder) {

    $scope.serverProcess = false;
    $scope.isUpdate = false;
    $scope.office = {};
    $scope.panelExpand = '';
    var title = 'Resumen Cargos';
    var fileName = 'ResumenCargos_' + moment().format('YYYYMMDD');
    var tableInstance = {};


    var getOffices = function () {
      server.getAll('offices').success(function (data) {
        $scope.offices = data;
      });
    };

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.office = {};
      $scope.officeForm.$setPristine();
      reloadData();
    };

    $scope.setDepartmentName = function () {
      $scope.office.departmentName = _($scope.departments).findWhere({ '_id': $scope.office.department_id }).name;
    };

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });

    var update = function(){
      $scope.serverProcess = true;
      server.update('offices', $scope.office, $scope.office._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('offices', $scope.office).success(function (result) {
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

    var editOffice = function(selectedOffice){
      $scope.isUpdate = true;
      $scope.office = selectedOffice;
      $scope.$digest();
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'code', title: 'Código Sectorial'},
      {field: 'name', title: 'Nombre Cargo'},
      {field: 'department.name', title: 'Departamento'},
      {field: 'basicSalary', title: 'Salario Básico', class: 'text-right'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('officesForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editOffice))
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withOption('dom', optionsDataTable.dom)
      .withOption('bProcessing', true)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'))
      .withOption("stateSave", true)
      .withOption('stateSaveCallback', optionsDataTable.saveState(title))
      .withOption('stateLoadCallback', optionsDataTable.loadState(title));

    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };


    $scope.delete = function () {
      SweetAlert.swal({
          title: "Está seguro de eliminar este cargo?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            $scope.serverProcess = true;
            server.delete('offices', $scope.office._id).success(function(result){
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

    $scope.maximize = function () {
      if ($scope.panelExpand == 'panel-expand') {
        $scope.panelExpand = '';
        $scope.dtOptions.scrollY = optionsDataTable.scrollY65;
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
    };

    getOffices();
    handlePanelAction();
  }
]);