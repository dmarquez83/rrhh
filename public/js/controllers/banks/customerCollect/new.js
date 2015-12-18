'use strict';
angular.module('app').controller('NewCustomerCollectCtrl', [
  '$scope',
  '$filter',
  '$compile',
  '$modal',
  'server',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  'DTInstances',
  function ($scope, $filter, $compile, $modal, server, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
    $scope.customers = [];
    $scope.newCustomerCollect = {
      total: 0,
      customer_id: ''
    };
    $scope.selectedQuotas = {};
    $scope.customerList = {};
    $scope.selectedQuotas.selected = {};
    $scope.selectAll = false;
    $scope.toggleAll = toggleAll;
    $scope.toggleOne = toggleOne;
    $scope.params = { 'customerIdentification': $scope.newCustomerCollect.customerIdentification };
    var fileName = 'deudas_cliente', tableInstance, customerQuotas = [];
    var titleHtml = '<input type="checkbox" ng-model="selectAll" ng-click="toggleAll()">';
    var getCustomers = function () {
      server.getAll('customer').success(function (data) {
        $scope.customerList.customers = data;
      });
    };
    $scope.datepickerOptions = {
      format: 'yyyy-mm-dd',
      language: 'es',
      autoclose: true
    };
    $scope.configSelectedCustomers = {
      create: false,
      valueField: 'identification',
      labelField: 'names',
      render: {
        item: function (item, escape) {
          return '<div>' + item.names + ' ' + item.surnames + '</div>';
        },
        option: function (item, escape) {
          return '<div>' + '<h6>' + item.names + ' ' + item.surnames + ' ' + '<small>' + escape(item.identification) + '</small>' + '</h6>' + '</div>';
        }
      },
      searchField: [
        'identification',
        'names',
        'surnames'
      ],
      placeholder: 'Seleccione un cliente',
      maxItems: 1
    };
    $scope.tableColumns = [
      DTColumnBuilder.newColumn(null).withTitle(titleHtml).notSortable().renderWith(function (data, type, full, meta) {
        customerQuotas.push(full);
        $scope.selectedQuotas.selected[full._id] = false;
        return '<input type="checkbox" ng-model="selectedQuotas.selected[' + '\'' + data._id + '\'' + ']" ng-click="toggleOne(selectedQuotas)">';
      }),
      DTColumnBuilder.newColumn('customerInvoiceNumber').withTitle('Numero de Factura'),
      DTColumnBuilder.newColumn('quotaNumber').withTitle('Numero Cuota'),
      DTColumnBuilder.newColumn('totalQuotaNumbers').withTitle('Total Cuotas'),
      DTColumnBuilder.newColumn('expireDate').withTitle('Fecha Vencimiento').renderWith(function (data) {
        return $filter('date')(data, 'yyyy-MM-dd');
      }),
      DTColumnBuilder.newColumn('expireDate').withTitle('Dias Retraso').renderWith(function (data) {
        var days = moment(data);
        var today = moment();
        var daysLate = days.diff(today, 'days');
        return daysLate;
      }),
      DTColumnBuilder.newColumn('paid').withTitle('Pagado'),
      DTColumnBuilder.newColumn('pendingPayment').withTitle('Pendiente de pago'),
      DTColumnBuilder.newColumn('status').withTitle('Estado'),
      DTColumnBuilder.newColumn('total').withTitle('Total Cuota'),
      DTColumnBuilder.newColumn('totalInvoice').withTitle('Total Factura')
    ];
    $scope.dtOptions = DTOptionsBuilder.newOptions().withOption('ajax', {
      url: 'customerQuotasForTable',
      type: 'POST',
      data: { 'customerIdentification': '' }
    }).withOption('order', [
      1,
      'asc'
    ]).withDataProp('data').withOption('serverSide', true).withOption('rowCallback', function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      var classValue = $('td', nRow).attr('class');
      var newClassValue = '';
      newClassValue = aData.status == 'Aprobada' ? 'success' : classValue;
      newClassValue = aData.status == 'Rechazada' ? 'danger' : newClassValue;
      $('td', nRow).addClass(newClassValue);
      $('td', nRow).unbind('click');
      $('td', nRow).on('dblclick', function () {
        $scope.$apply(function () {
          $scope.opensalesOrder(aData);
        });
      });
      return nRow;
    }).withOption('createdRow', function (row, data, dataIndex) {
      $compile(angular.element(row).contents())($scope);
    }).withOption('headerCallback', function (header) {
      if (!$scope.headerCompiled) {
        $scope.headerCompiled = true;
        $compile(angular.element(header).contents())($scope);
      }
    }).withOption('iDisplayLength', 25).withOption('deferRender', true).withOption('scrollY', $(window).height() * 0.9 * 0.6).withTableTools('/../../../bower_components/datatables-tabletools/swf/copy_csv_xls_pdf.swf').withTableToolsButtons([
      {
        'sExtends': 'copy',
        'sButtonText': 'Copiar',
        'sButtonClass': 'btn btn-white btn-sm'
      },
      {
        'sExtends': 'csv',
        'sButtonText': 'CSV',
        'sButtonClass': 'btn btn-white btn-sm',
        'sFileName': fileName + '.csv'
      },
      {
        'sExtends': 'xls',
        'sButtonText': 'Excel',
        'sButtonClass': 'btn btn-white btn-sm',
        'sFileName': fileName + '.xls'
      },
      {
        'sExtends': 'pdf',
        'sButtonText': 'PDF',
        'sButtonClass': 'btn btn-white btn-sm',
        'sFileName': fileName + '.pdf'
      }
    ]);
    DTInstances.getLast().then(function (dtInstance) {
      tableInstance = dtInstance;
    });
    $scope.reloadData = function () {
      $scope.params = { 'customerIdentification': angular.copy($scope.newCustomerCollect.customerIdentification) };
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
      $scope.newCustomerCollect.total = total;
    }
    ;
    $scope.save = function () {
      var modalInstance = $modal.open({
          templateUrl: '../../views/banks/customerCollect/paymentMethods.html',
          controller: 'PaymentMethodsCtrl',
          size: 'lg',
          resolve: {
            totalCollect: function () {
              var data = {
                  totalCollect: angular.copy($scope.newCustomerCollect.total),
                  quotasSelected: angular.copy($scope.selectedQuotas.selected)
                };
              return data;
            }
          }
        });
      $scope.clean = function () {
        $scope.newCustomerCollect.customerIdentification = '';
        $scope.newCustomerCollect.total = 0;
        $scope.reloadData();
      };
      modalInstance.result.then(function () {
        $scope.reloadData();
        $scope.newCustomerCollect.total = 0;
      });
    };
    $scope.maximize = function () {
      if ($('body').hasClass('panel-expand')) {
        $scope.dtOptions.scrollY = $(window).height() * 0.5 * 0.6;
      } else {
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
      tableInstance.rerender();
    };
    getCustomers();
    handlePanelAction();
  }
]);