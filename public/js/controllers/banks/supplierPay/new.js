'use strict';
angular.module('app').controller('SummarySupplierPayCtrl', [
  '$scope',
  '$filter',
  '$compile',
  '$modal',
  'server',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  'transferData',
  'optionsDataTable',
  function ($scope, $filter, $compile, $modal, server, DTOptionsBuilder, DTColumnBuilder, transferData, optionsDataTable) {
    $scope.suppliers = [];
    $scope.newSupplierPay = {
      total: 0,
      customer_id: ''
    };

    $scope.selectedQuotas = {};
    $scope.supplierList = {};
    $scope.selectedQuotas.selected = {};
    $scope.selectAll = false;
    $scope.toggleAll = toggleAll;
    $scope.toggleOne = toggleOne;
    var filters = transferData.data; 
    var fileName = 'deudas_proveedores_' + moment().format('YYYYMMDD');
    var title = 'Deudas Proveedores';
    var tableInstance = {};
    var customerQuotas = [];

    var titleHtml = '<input type="checkbox" ng-model="selectAll" ng-click="toggleAll()">';

    var openSupplierPay = function(selectedSupplierPay){
      var modalOpen = $modal.open({
          templateUrl: '../../views/banks/supplierPay/details.html',
          controller: 'DetailsSupplierPayCtrl',
          size: 'lg',
          resolve: {
            selectedSupplierPay: function () {
              return selectedSupplierPay;
            }
          }
        });

      modalOpen.result.then(function () {
        tableInstance.reloadData();
      });
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };


    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: null, title: titleHtml, render: function (data, type, full) {
        customerQuotas.push(full);
        $scope.selectedQuotas.selected[full._id] = false;
        return '<input type="checkbox" ng-model="selectedQuotas.selected[' + '\'' + data._id + '\'' + ']" ng-click="toggleOne(selectedQuotas)">';
      }},
      {field: 'supplierInvoiceNumber', title: 'Número de Factura'},
      {field: 'supplier', title: 'Proveedor', filter: 'businessPartner'},
      {field: 'quotaNumber', title: 'Número de Cuota', class: 'text-right'},
      {field: 'totalQuotaNumbers', title: 'Total Cuotas', class: 'text-right'},
      {field: 'expireDate', title: 'Fecha Vencimiento', filter: 'date'},
      {field: 'expireDate', title: 'Dias de Retraso', render: function(data){
        var days = moment(data);
        var today = moment();
        var daysLate = days.diff(today, 'days');
        return daysLate;
      }, class: 'text-right'},
      {field: 'paid', title: 'Pagado', filter: 'currency', defaultContent: '0.00', class: 'text-right'},
      {field: 'pendingPayment', title: 'Pendiente de pago', filter: 'currency', defaultContent: '0.00', class: 'text-right'},
      {field: 'total', title: 'Total Cuota', filter: 'currency', defaultContent: '0.00', class: 'text-right'},
      {field: 'totalInvoice', title: 'Total Factura', filter: 'currency', defaultContent: '0.00', class: 'text-right'},
    ]);

    $scope.reloadData = function () {
      $scope.params = { 'supplierIdentification': angular.copy($scope.newSupplierPay.supplierIdentification) };
      $scope.dtOptions.ajax.data = $scope.params;
      tableInstance.reloadData();
    };

    function toggleAll() {
      _($scope.selectedQuotas.selected).each(function (item, key) {
        $scope.selectedQuotas.selected[key] = angular.copy($scope.selectAll);
      });
      sumQuotasForPay();
    }

    function toggleOne(selectedItems) {
      $scope.selectAll = true;
      _($scope.selectedQuotas.selected).each(function (item, key) {
        if (item == false) {
          $scope.selectAll = false;
        }
      });
      sumQuotasForPay();
    }

    function sumQuotasForPay() {
      var total = 0;
      customerQuotas = _(customerQuotas).uniq();
      _($scope.selectedQuotas.selected).each(function (selectedQuota, key) {
        if (selectedQuota == true) {
          total += parseFloat(_(customerQuotas).findWhere({ '_id': key }).total);
        }
      });
      $scope.newSupplierPay.total = total;
    }

    $scope.save = function () {
      var modalInstance = $modal.open({
          templateUrl: '../../views/banks/supplierPay/paymentMethods.html',
          controller: 'PaymentMethodsCtrl',
          size: 'lg',
          resolve: {
            totalPay: function () {
              var data = {
                  totalPay: angular.copy($scope.newSupplierPay.total),
                  paysSelected: angular.copy($scope.selectedQuotas.selected)
                };
              console.log(data);
              return data;
            }
          }
        });
      $scope.clean = function () {
        $scope.newSupplierPay.supplierIdentification = '';
        $scope.newSupplierPay.total = 0;
        $scope.reloadData();
      };
      modalInstance.result.then(function () {
        $scope.reloadData();
        $scope.newSupplierPay.total = 0;
      });
    };

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('supplierPays/ForTable', filters))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(openSupplierPay))
      .withOption('headerCallback', function (header) {
        if (!$scope.headerCompiled) {
          $scope.headerCompiled = true;
          $compile(angular.element(header).contents())($scope);
        }
      })
      .withOption('createdRow', function (row) {
        $compile(angular.element(row).contents())($scope);
      })
      .withOption('order', [3, 'desc'])
      .withOption('iDisplayLength', 50)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY80)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    $scope.maximize = function () {
      if ($('body').hasClass('panel-expand')) {
        $scope.dtOptions.scrollY = optionsDataTable.scrollY80;
      } else {
        $scope.dtOptions.scrollY = optionsDataTable.scrollY100;
      }
      tableInstance.rerender();
    };

    handlePanelAction();
  }
]);