'use strict';
angular.module('app').controller('SummaryImportQuotationConsolidationCtrl', [
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
  '$timeout',
  function ($scope, $filter, $modal, DTOptionsBuilder, DTColumnBuilder, server, transferData,
            optionsDataTable, dataTablePDFMaker, dataTableXLSXMaker, $timeout) {

    $scope.panelExpand = '';
    var fileName = 'resumen_solicitud_importaciones_' + moment().format('YYYYMMDD');
    var title = 'Resumen Solicitudes de Importación-' + moment().format('YYYYMMDD');
    var tableInstance = {};

    var getCustomers = function () {
      server.getAll('customer').success(function (data) {
        transferData.customers = data;
      });
    };

    var openImportQuotation = function (selectedImportQuotation) {
      var modalInstance = $modal.open({
        templateUrl: '../../views/imports/importQuotation/details.html',
        controller: 'ImportQuotationDetailsCtrl',
        windowClass: 'xlg',
        resolve: {
          selectedData: function () {
            return {
              'isFromLink': true,
              'documentNumber': selectedImportQuotation.number,
              'documentName': 'importQuotation'
            };
          }
        }
      });
      modalInstance.result.then(function () {
        reloadData();
      });
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'creationDate', title: 'Fecha Creación', filter: 'date'},
      {field: 'requiredDate', title: 'Fecha Requerida', filter: 'date'},
      {field: 'number', title: 'N\xfamero'},
      {field: 'customer.identification', title: 'Identificación'},
      {field: 'customer', title: 'Cliente', filter: 'businessPartner'},
      {field: 'status', title: 'Estado'},
      {field: 'productsNames', title: 'Productos'},
      {field: 'suppliers', title: 'Proveedores'},
      {field: 'products', title: 'Cantidad total', render: function (data) {
        var totalQuantity = 0;
        _(data).each(function(product){
          totalQuantity = math.add(totalQuantity, product.quantity);
        });
        return $filter('number')(totalQuantity, 2);
      }, defaultContent: '0.00', class: 'text-right'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('importQuotationWithSuppliersForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(openImportQuotation))
      .withOption('order', [3, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY60)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.$on('reloadImportQuotationTable', function () {
      reloadData();
    });

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.maximize = function () {
      if ($scope.panelExpand === 'panel-expand') {
        $scope.panelExpand = '';
        $scope.dtOptions.scrollY = optionsDataTable.scrollY60;
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.scrollY = optionsDataTable.scrollY90;
      }
    };

    getCustomers();
  }
]);
