'use strict';
angular.module('app').controller('ImportOrdersModalCtrl', [
  '$scope',
  '$modal',
  '$compile',
  '$modalInstance',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  '$filter',
  'server',
  'selectedData',
  'transferData',
  'optionsDataTable',
  function ($scope, $modal, $compile, $modalInstance, DTOptionsBuilder, DTColumnBuilder, $filter, server, selectedData, transferData, optionsDataTable) {

    var selectProduct = function(selectedProduct){
      $modalInstance.close(selectedProduct);
    };
    var fileName = 'resumen_pedidos_importación_' + moment().format('YYYYMMDD');
    var title = 'Resumen Pedidos Importación -' + moment().format('YYYYMMDD');
    var parameters = {'supplier_id' : selectedData.specialImportOrdersSupplier};

    var openImportOrder = function(selectedImportOrder) {
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

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('importOrderForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(openImportOrder))
      .withOption('order', [1, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY60)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'))
      .withOption('createdRow', function(row, data) {
        $compile(row)($scope);
      });


    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: null, title: '', render: function(data) {
        var selectedOrder = angular.toJson(data);
        var button = '<checkbox ng-click="selectPurchaseOrder(\''+ encodeURIComponent(selectedOrder) +'\')" '
          + ' ng-model="importOrdersSelected[\''+data.number+'\']"></checkbox>';
        return button;
      }},
      {field: 'creationDate', title: 'Fecha Documento', filter: 'date'},
      {field: 'number', title: 'N\xfamero'},
      {field: 'supplier', title: 'Proveedor', filter: 'businessPartner'},
      {field: 'status', title: 'Estado'},
      {field: 'totals.subtotal', title: 'Subtotal', filter: 'currency', class: 'text-right'},
      {field: 'totals.total', title: 'Total', filter: 'currency', class: 'text-right'},
    ]);

    $scope.ok = function () {
      $modalInstance.close();
    };

    $scope.cancel = function () {
      $modalInstance.dismiss('cancel');
    };

  }
]);
