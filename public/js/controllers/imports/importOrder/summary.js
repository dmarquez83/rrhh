'use strict';
angular.module('app').controller('SummaryImportOrderCtrl', [
  '$scope',
  '$filter',
  '$modal',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  'server',
  'transferData',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  function ($scope, $filter, $modal, DTOptionsBuilder, DTColumnBuilder, server, transferData,
            optionsDataTable, dataTablePDFMaker, dataTableXLSXMaker) {

    $scope.panelExpand = '';
    var fileName = 'resumen_pedidos_' + moment().format('YYYYMMDD');
    var title = 'Resumen Pedidos -' + moment().format('YYYYMMDD');
    var tableInstance = {};

    var getCustomers = function () {
      server.getAll('customer').success(function (data) {
        transferData.customers = data;
      });
    };

    var getApprovalFlow = function() {
      var parameter = {parameter: 'documentCode', value: '007'};
      server.getByParameterPost('documentsApprovalFlows', parameter).success(function(data){
        transferData.importOrderApprovalFlow = ( _(data).has('flow') ? _(data.flow).sortBy('order') : []);
      });
    };

    var openImportOrder = function (selectedImportOrder) {
      var modalInstance = $modal.open({
        templateUrl: '../../views/imports/importOrder/details.html',
        controller: 'ImportOrderDetailsCtrl',
        windowClass: 'xlg',
        resolve: {
          selectedData: function () {
            return {
              'isFromLink': false,
              'document': selectedImportOrder};
          }
        }
      });
      modalInstance.result.then(function () {
        reloadData();
      });
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'creationDate', title: 'Fecha Creación', filter: 'date'},
      {field: 'deliveryDate', title: 'Fecha Entrega', filter: 'date'},
      {field: 'number', title: 'N\xfamero'},
      {field: 'supplier.identification', title: 'Identificación'},
      {field: 'supplier', title: 'Proveedor', filter: 'businessPartner'},
      {field: 'status', title: 'Estado'},
      {field: 'totals.subtotal', title: 'Subtotal', filter: 'currency', class: 'text-right'},
      {field: 'totals.subtotalIva', title: 'Subtotal 12%', filter: 'currency', class: 'text-right'},
      {field: 'totals.subtotalIvaZero', title: 'Subtotal 0%', filter: 'currency', class: 'text-right'},
      {field: 'totals.totalDiscount', title: 'Descuento', filter: 'currency', class: 'text-right'},
      {field: 'totals.totalIVA', title: 'IVA', filter: 'currency', class: 'text-right'},
      {field: 'totals.total', title: 'Total', filter: 'currency', class: 'text-right'},
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('importOrderForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(openImportOrder))
      .withOption('order', [2, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY60)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.$on('reloadImportOrderTable', function () {
      reloadData();
    });

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.maximize = function () {
      if ($scope.panelExpand === 'panel-expand') {
        $scope.panelExpand = '';
        $scope.dtOptions.scrollY = $(window).height() * 0.5 * 0.6;
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
    };

    getCustomers();
    getApprovalFlow();
  }
]);
