'use strict';
angular.module('app').controller('PaymentWayCtrl', [
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
    $scope.paymentWay = {};
    $scope.paymentWays = [];
    $scope.panelExpand = '';
    var fileName = 'resumen_formas_de_pago_' + moment().format('YYYYMMDD');
    var title = 'Resumen Formas De Pago';
    var tableInstance = {};


    var update = function(){
      $scope.serverProcess = true;
      server.update('paymentWays', $scope.paymentWay, $scope.paymentWay._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('paymentWays', $scope.paymentWay).success(function (data) {
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

    var editPaymentWay = function(selectedPaymentWay){
      $scope.isUpdate = true;
      $scope.paymentWay = selectedPaymentWay;
      $scope.$digest();
    };

    $scope.tableColumns = optionsDataTable.generateTableColumns([
      {'field': 'code', 'value': 'Código'},
      {'field': 'name', 'value': 'Nombre'},
      {'field': 'description', 'value': 'descripción'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('paymentWays/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editPaymentWay))
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

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar esta Forma de Pago?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('paymentWays', $scope.paymentWay._id).success(function(result){
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

    $scope.clean = function(){
      $scope.isUpdate = false;
      $scope.serverProcess = false;
      $scope.paymentWay = {};
      $scope.paymentWayForm.$setPristine();
      reloadData();
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
