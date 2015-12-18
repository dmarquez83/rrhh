'use strict';
angular.module('app').controller('SummaryImportQuotationCtrl', [
  '$scope',
  '$filter',
  '$modal',
  '$http',
  'DTOptionsBuilder',
  'server',
  'transferData',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  function ($scope, $filter, $modal, $http, DTOptionsBuilder, server, transferData, optionsDataTable) {

    $scope.panelExpand = '';
    var fileName = 'resumen_solicitud_importaciones_' + moment().format('YYYYMMDD');
    var title = 'Reporte Solicitudes de Importaciones';
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
              'isFromLink': false,
              'document': selectedImportQuotation};
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
      {field: 'sellerName', title: 'Vendedor'},
      {field: 'number', title: 'N\xfamero'},
      {field: 'customer.identification', title: 'Identificación'},
      {field: 'customer', title: 'Cliente', filter: 'businessPartner'},
      {field: 'status', title: 'Estado'},
      {field: 'products', title: 'Cantidad total', render: function (data) {
        var totalQuantity = 0;
        _(data).each(function(product){
          totalQuantity = math.add(totalQuantity, product.quantity);
        });
        return $filter('number')(totalQuantity, 2);
      }, defaultContent: '0.00', class: 'text-right'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('importQuotationForTable'))
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
        $scope.dtOptions.withOption('scrollY', optionsDataTable.scrollY60);
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.withOption('scrollY', optionsDataTable.scrollY90);
      }
    };

    getCustomers();
  }
]);
