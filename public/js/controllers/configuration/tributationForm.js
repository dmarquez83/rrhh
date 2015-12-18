'use strict';
angular.module('app').controller('FormTributationConfigurationCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  'transferData',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, server, SweetAlert, transferData, optionsDataTable, DTOptionsBuilder) {

    $scope.isUpdate = false;
    $scope.isServerProcess = false;
    $scope.configurationTributationForm = {};
    $scope.configurationTributationForms = [];
    $scope.panelExpand = '';
    $scope.canSelectMonth = '';
    var fileName = 'resumen_formularios_' + moment().format('YYYYMMDD');
    var title = 'Resumen Formularios Tributación';
    var tableInstance = {};

    var save = function(){
      $scope.serverProcess = true;
      server.save('configurationTributationForms', $scope.configurationTributationForm).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var update = function(){
      $scope.serverProcess = true;
      server.update('configurationTributationForms', $scope.configurationTributationForm, $scope.configurationTributationForm._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    $scope.save = function(formIsValid) {
      if(formIsValid) {
        $scope.serverProcess = true;
        if($scope.isUpdate === true){
          update();
        }else {
          save();
        }
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    };

    var editPaymentMethod = function(selectedConfigurationTributationForm){
      $scope.isUpdate = true;
      $scope.configurationTributationForm = selectedConfigurationTributationForm;
      $scope.$digest();
    };


    $scope.tableColumns = optionsDataTable.generateTableColumns([
      {'field': 'name', 'value': 'Nombre'},
      {'field': 'description', 'value': 'Descripción'},
      {'field': 'fileName', 'value': 'Nombre Archivo'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('configurationTributationForms/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editPaymentMethod))
      .withOption('order', [0, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));


    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.clean = function(){
      $scope.isUpdate = false;
      $scope.serverProcess = false;
      $scope.configurationTributationForm = {};
      $scope.configurationTributationFormForm.$setPristine();
      document.getElementById("configurationTributationFormForm").reset();
      reloadData();
    };

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este Formulario?",
          text: "Si elimina este formulario no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('configurationTributationForms', $scope.configurationTributationForm._id).success(function(result){
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
