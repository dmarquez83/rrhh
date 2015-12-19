'use strict';
angular.module('app').controller('EmployeesReportCtrl', [
  '$scope',
  '$modal',
  'DTOptionsBuilder',
  'server',
  'transferData',
  'optionsDataTable',
  function ($scope, $modal, DTOptionsBuilder, server, transferData, optionsDataTable) {

    handlePanelAction();
    $scope.panelExpand = '';
    $scope.isLoading = false;
    $scope.employees = [];
    var title = 'Resumen Empleados';
    var fileName = 'resumen_empleados' + moment().format('YYYYMMDD');
    var tableInstance = {};
    var tableInstance = {};

    var fileName = 'ResumenEmpleados_' + moment().format('YYYYMMDD');

    var editEmployee = function (selectedEmployee) {
      var modalInstance = $modal.open({
        templateUrl: '../../views/humanResources/employees/employeDetails.html',
        controller: 'EditEmployeeCtrl',
        windowClass: 'xlg',
        resolve: {
          selectedEmployee: function () {
            return selectedEmployee;
          }
        }
      });
      modalInstance.result.then(function () {
        reloadData();
      });
    };

    server.getAll('departments').success(function (data) {
      transferData.data.departments = data;
    });

    server.getAll('bank').success(function (data) {
      transferData.data.banks = data;
    });

    server.getAll('offices').success(function (data) {
      transferData.data.offices = data;
    });

    server.getAll('maritalStatus').success(function (data) {
      transferData.data.maritalsStatus = data;
    });

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'identification', title: 'Identificación'},
      {field: 'code', title: 'Código'},
      {field: 'names', title: 'Nombre'},
      {field: 'surnames', title: 'Apellido'},
      {field: 'maritalStatus.name', title: 'Estado Marital'},
      {field: 'birthday', title: 'Fecha Nacimiento', filter: 'date'},
      {field: 'telephones', title: 'Telefonos'},
      {field: 'cellphones', title: 'Celulares'},
      {field: 'emails', title: 'Correo'},
      {field: 'grossSalary', title: 'Salario'},
      {field: 'department.name', title: 'Departamento'},
      {field: 'office.name', title: 'Cargo'},
      {field: 'status', title: 'Estado'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('employeeForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editEmployee))
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY80)
      .withOption('dom', optionsDataTable.dom)
      .withOption('bProcessing', true)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'))
      .withOption("stateSave", true)
      .withOption('stateSaveCallback', optionsDataTable.saveState(title))
      .withOption('stateLoadCallback', optionsDataTable.loadState(title));

    var reloadData = function(){
      tableInstance.reloadData();
    }

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.maximize = function () {
      if ($scope.panelExpand == 'panel-expand') {
        $scope.panelExpand = '';
        $scope.dtOptions.scrollY = $(window).height() * 0.9 * 0.7;
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
    };

  }
]);