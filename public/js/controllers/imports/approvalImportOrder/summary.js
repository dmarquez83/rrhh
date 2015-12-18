'use strict';
angular.module('app').controller('SummaryApprovalImportOrderCtrl', [
  '$scope',
  '$filter',
  '$modal',
  'DTOptionsBuilder',
  'server',
  'transferData',
  'optionsDataTable',
  function ($scope, $filter, $modal, DTOptionsBuilder, server, transferData, optionsDataTable) {
    var tableInstance = {};
    var fileName = 'resumen_importación_para_aprobación' + moment().format('YYYYMMDD');
    var title = 'Resumen de Importaciones para Aprobación - ' + moment().format('YYYYMMDD');

    var openImportOrder = function (selectedImportOrder) {
      var modalInstance = $modal.open({
        templateUrl: '../../views/imports/approvalImportOrder/details.html',
        controller: 'ApprovalImportQuotationDetailsCtrl',
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
        tableInstance.reloadData();
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
      .fromSource(optionsDataTable.fromSource('importOrderforApproval'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(openImportOrder))
      .withOption('order', [2, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY70)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.maximize = function () {
      if ($('body').hasClass('panel-expand')) {
        $('body').removeClass('panel-expand');
        $('#summaryApprovalImportOrder').removeClass('panel-expand');
        $scope.dtOptions.scrollY = $(window).height() * 0.9 * 0.7;
      } else {
        $('body').addClass('panel-expand');
        $('#summaryApprovalImportOrder').addClass('panel-expand');
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
      tableInstance.rerender();
    };


    handlePanelAction();
  }
]);
