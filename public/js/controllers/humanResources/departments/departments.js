'use strict';
angular.module('app').controller('DepartmentsCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, server, SweetAlert, optionsDataTable, DTOptionsBuilder) {

    $scope.serverProcess = false;
    $scope.isUpdate = false;
    $scope.department = {};
    $scope.panelExpand = '';
    var title = 'Resumen Departamentos';
    var fileName = 'ResumenDepartamentos_' + moment().format('YYYYMMDD');
    var tableInstance = {};

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.department = {};
      $scope.departmentForm.$setPristine();
      reloadData();
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('departments', $scope.department, $scope.department._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('departments', $scope.department).success(function (result) {
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

    var editDepartment = function(selectedDepartment){
      $scope.isUpdate = true;
      $scope.department = selectedDepartment;
      $scope.$digest();
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'code', title: 'Código'},
      {field: 'name', title: 'Nombre del Departamento'},
      {field: 'description', title: 'Descripción'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('departmentsForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editDepartment))
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
          title: "Está seguro de eliminar este departamento?",
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
            server.delete('departments', $scope.department._id).success(function(result){
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

    handlePanelAction();
  }
]);