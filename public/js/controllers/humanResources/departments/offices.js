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
    };

    $scope.tableColumns = [
      DTColumnBuilder.newColumn('code').withTitle('Código Sectorial').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('name').withTitle('Nombre Cargo').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('department.name').withTitle('Departamento').withOption('defaultContent', ''),
      DTColumnBuilder.newColumn('basicSalary').withTitle('Salario Básico').renderWith(function (data) {
        return $filter('number')(data, 2);
      }).withClass('text-right').withOption('defaultContent', '')
    ];

    $scope.dtOptions = DTOptionsBuilder.newOptions()
      .withOption('ajax', {
        url: 'officesForTable',
        type: 'POST',
        headers: {'X-CSRF-Token': CSRF_TOKEN}
      })
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', function (nRow, aData) {
        $('td', nRow).unbind('click');
        $('td', nRow).bind('dblclick', function () {
          $scope.$apply(function () {
            editOffice(aData);
          });
        });
        return nRow;
      })
      .withOption('order', [2, 'asc'])
      .withOption('iDisplayLength', 10)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withTableTools(optionsDataTable.urlTableTools)
      .withTableToolsButtons([
        {
          "sExtends":    "div",
          "sDiv":        "copy",
          "sButtonText": "PDF",
          'sButtonClass': 'btn btn-white btn-sm',
          "fnClick": function (nButton, oConfig, oFlash) {
            dataTablePDFMaker.make('A4', 'landscape',
              'Resumen Cargos - ' + moment().format('YYYY-MM-DD HH:mm:ss'),
              'resumen_cargos_' + moment().format('YYYYMMDD_HHmmss'),
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
              'Resumen Cargos'+ moment().format('YYYY-MM-DD HH:mm:ss'),
              'resumen_cargos_' + moment().format('YYYYMMDD_HHmmss'),
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
            dataTablePDFMaker.make('A4', 'landscape',
              'Resumen Cargos - ' + moment().format('YYYY-MM-DD HH:mm:ss'),
              'resumen_cargos_' + moment().format('YYYYMMDD_HHmmss'),
              tableInstance.DataTable.context[0].aoColumns,
              tableInstance.dataTable.fnGetData(),
              'print'
            );
          }
        },
      ]);

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