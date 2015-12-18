'use strict';
angular.module('app').controller('PaymentConditionCtrl', [
  '$scope',
  '$filter',
  'documentValidate',
  'server',
  'transferData',
  'SweetAlert',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  'DTOptionsBuilder',
  function ($scope,  $filter, documentValidate, server, transferData, SweetAlert, optionsDataTable, dataTablePDFMaker,
            dataTableXLSXMaker, DTOptionsBuilder) {


    $scope.serverProcess = false;
    $scope.isUpdate = false;
    $scope.paymentCondition = {};
    $scope.paymentConditions = [];
    $scope.panelExpand = '';
    $scope.paymentCondition.days = [''];
    var fileName = 'resumen_condiciones_de_pago_' + moment().format('YYYYMMDD');
    var title = 'Resumen Condiciones De Pago';
    var tableInstance = {};


    var update = function(){
      $scope.serverProcess = true;
      server.update('paymentConditions', $scope.paymentCondition, $scope.paymentCondition._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.clean();
        }
      });
    };

    var save = function(){
      $scope.serverProcess = true;
      server.save('paymentConditions', $scope.paymentCondition).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.clean();
        }
      });
    };

    $scope.save = function (formIsValid) {
      if(formIsValid){
        if ($scope.isUpdate) {
          update();
        } else {
          save();
        }
      } else{
        toastr.warning('Revisar errores en el formulario');
      }
    };

    var editPaymentCondition = function(selectedPaymentCondition){
      $scope.isUpdate = true;
      $scope.paymentCondition = angular.copy(selectedPaymentCondition);
      $scope.$digest();
    };

    $scope.tableColumns = optionsDataTable.generateTableColumns([
      {'field': 'name', 'value': 'Nombre Condición de Pago'},
      {'field': 'days', 'value': 'Días de pago'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('paymentConditions/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editPaymentCondition))
      .withOption('order', [0, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));


    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.getTableIntance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.delete = function () {
      SweetAlert.swal({
          title: 'Está seguro de eliminar esta condición de pago?',
          text: 'Si elimina este registro no lo podrá recuperar',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#DD6B55',confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No, cancelar',
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            $scope.serverProcess = true;
            server.delete('paymentConditions',  $scope.paymentCondition._id).success(function(result){
              $scope.serverProcess = false;
              if(result.type === 'success') {
                $scope.serverProcess = false;
                SweetAlert.swal('Eliminado!', result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal('Error!', result.msg, result.type);
              }
            });
          }
        });
    };

    $scope.addDays = function () {
      $scope.paymentCondition.days.push('');
    };
    $scope.deleteDay = function (index) {
      $scope.paymentCondition.days.splice(index, 1);
    };

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.paymentCondition = {};
      $scope.paymentCondition.days = [''];
      $scope.paymentConditionForm.$setPristine();
      reloadData();
    };

    $scope.maximize = function () {
      if ($scope.panelExpand === 'panel-expand') {
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
