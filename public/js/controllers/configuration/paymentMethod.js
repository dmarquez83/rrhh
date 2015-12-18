'use strict';
angular.module('app').controller('PaymentMethodCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  'transferData',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  'DTOptionsBuilder',
  function ($scope, server, SweetAlert, transferData, optionsDataTable, dataTablePDFMaker, dataTableXLSXMaker, DTOptionsBuilder) {

    $scope.isUpdate = false;
    $scope.isServerProcess = false;
    $scope.paymentMethod = {};
    $scope.paymentMethods = [];
    $scope.panelExpand = '';
    var fileName = 'resumen_métodos_de_pago_' + moment().format('YYYYMMDD');
    var title = 'Resumen Métodos De Pago';
    var tableInstance = {};

    server.getAll('paymentWays').success(function (data) {
      $scope.paymentWays = data;
    });

    var save = function(){
      $scope.serverProcess = true;
      server.save('paymentMethods', $scope.paymentMethod).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var update = function(){
      $scope.serverProcess = true;
      server.update('paymentMethods', $scope.paymentMethod, $scope.paymentMethod._id).success(function (data) {
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

    var editPaymentMethod = function(selectedPaymentMethod){
      $scope.isUpdate = true;
      $scope.paymentMethod = selectedPaymentMethod;
      $scope.$digest();
    };


    $scope.tableColumns = optionsDataTable.generateTableColumns([
      {'field': 'code', 'value': 'Código'},
      {'field': 'name', 'value': 'Nombre'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('paymentMethods/forTable'))
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
      $scope.paymentMethod = {};
      $scope.paymentMethodForm.$setPristine();
      reloadData();
    };

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este Método de Pago?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('paymentMethods', $scope.paymentMethod._id).success(function(result){
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
