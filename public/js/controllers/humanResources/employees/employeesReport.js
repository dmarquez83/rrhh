'use strict';
angular.module('app').controller('EmployeesReportCtrl', [
  '$scope',
  '$modal',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  'server',
  'transferData',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  function ($scope, $modal, DTOptionsBuilder, DTColumnBuilder, server, transferData, optionsDataTable, dataTablePDFMaker, dataTableXLSXMaker) {
    handlePanelAction();
    $scope.panelExpand = '';
    $scope.isLoading = false;
    $scope.employees = [];
    var tableInstance = {};

    var fileName = 'ResumenEmpleados_' + moment().format('YYYYMMDD');

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

    $scope.tableColumns = [
      DTColumnBuilder.newColumn('identification').withTitle('C&eacute;dula').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('code').withTitle('Código').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('names').withTitle('Nombres').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('surnames').withTitle('Apellidos').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('birthProvince').withTitle('Provincia Nacimiento').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('birthCity').withTitle('Ciudad Nacimiento').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('birthday').withTitle('Cumpleaños').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('addressCity').withTitle('Ciudad Domicilio').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('address').withTitle('Dirección').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('telephones').withTitle('Teléfono').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('cellphones').withTitle('Celular').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('emails').withTitle('Email').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('gender').withTitle('Género').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('disability').withTitle('Tiene Discapacidad').renderWith(function (data) {
        var result = (data === true ? 'Si' : 'No');
        return result;
      }).withClass('text-right').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('conadis').withTitle('Conadis').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('maritalStatus.name').withTitle('Estado Civil').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('spouseName').withTitle('Conyuge').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('sonNumber').withTitle('# Hijos').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('responsibilities').withTitle('# Cargas').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('paymentMethod').withTitle('Forma de Pago').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('bank.name').withTitle('Banco').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('bankAcountNumber').withTitle('Número Cuenta').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('bankAccountType').withTitle('Tipo de Cuenta').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('grossSalary').withTitle('Sueldo Bruto').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('department.name').withTitle('Departamento').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('office.name').withTitle('Cargo').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('profession').withTitle('Profesión').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('status').withTitle('Estado').withOption('defaultContent', '')
    ];

    $scope.dtOptions = DTOptionsBuilder.newOptions()
      .withOption('ajax', {
        url: 'employeeForTable',
        type: 'POST',
        headers: {'X-CSRF-Token': CSRF_TOKEN}
      })
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      $('td', nRow).unbind('click');
      $('td', nRow).bind('dblclick', function () {
        $scope.$apply(function () {
          editEmployee(aData);
        });
      });
      return nRow;
    })
      .withOption('order', [2, 'asc'])
      .withColVis()
      .withColVisOption('buttonText' , 'Selecionar Columnas')
      .withOption('stateSave', true)
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY70)
      .withOption('scrollX', true)
      .withTableTools(optionsDataTable.urlTableTools)
      .withTableToolsButtons([
        {
          "sExtends":    "div",
          "sDiv":        "copy",
          "sButtonText": "PDF",
          'sButtonClass': 'btn btn-white btn-sm',
          "fnClick": function (nButton, oConfig, oFlash) {
            dataTablePDFMaker.make('A4', 'landscape', 'Resumen Empleados - ' + moment().format('YYYY-MM-DD HH:mm:ss'),
              'resumen_empleados_' + moment().format('YYYYMMDD_HHmmss'),
              tableInstance.DataTable.context[0].aoColumns,
              tableInstance.dataTable.fnGetData(),
              'file'
            );
          }
        },
        {
          "sExtends":    "div",
          "sDiv":        "copy",
          "sButtonText": "Excel",
          'sButtonClass': 'btn btn-white btn-sm',
          "fnClick": function (nButton, oConfig, oFlash) {
            dataTableXLSXMaker.make(
              'Resumen Empleados',
              'resumen_empleados_' + moment().format('YYYYMMDD_HHmmss'),
              tableInstance.DataTable.context[0].aoColumns,
              tableInstance.dataTable.fnGetData()
            );
          }
        },
        {
          'sExtends': 'csv',
          'sButtonText': 'CSV',
          'sButtonClass': 'btn btn-white btn-sm',
          'sFileName': fileName + '.csv'
        },
        {
          "sExtends":    "div",
          "sDiv":        "copy",
          "sButtonText": "Imprimir",
          'sButtonClass': 'btn btn-white btn-sm',
          "fnClick": function (nButton, oConfig, oFlash) {
            dataTablePDFMaker.make('A4', 'landscape', 'Resumen Empleados - ' + moment().format('YYYY-MM-DD HH:mm:ss'),
              'resumen_empleados_' + moment().format('YYYYMMDD_HHmmss'),
              tableInstance.DataTable.context[0].aoColumns,
              tableInstance.dataTable.fnGetData(),
              'print'
            );
          }
        }
      ]);

    var reloadData = function(){
      tableInstance.reloadData();
    }

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

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