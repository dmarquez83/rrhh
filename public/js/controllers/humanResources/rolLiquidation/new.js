'use strict';
angular.module('app').controller('RolLiquidationCtrl', [
  '$scope',
  '$filter',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  'DTInstances',
  'server',
  function ($scope, $filter, DTOptionsBuilder, DTColumnBuilder, DTInstances, server) {
    $scope.employees = [];
    $scope.filter = {};
    $scope.filter.startDate = moment().startOf('month').format('YYYY-MM-DD');
    $scope.filter.endDate = moment().endOf('month').format('YYYY-MM-DD');
    var fileName = 'liquidacionRol' + moment().format('YYYY-MM-DD'), tableInstance;
    var totalPayEmployess = [];
    $scope.tableColumns = [
      DTColumnBuilder.newColumn('identification').withTitle('C\xe9dula'),
      DTColumnBuilder.newColumn('names').withTitle('Nombre').renderWith(function (data, display, full) {
        return full.names + full.surnames;
      }),
      DTColumnBuilder.newColumn('grossSalary').withTitle('Sueldo B\xe1sico').renderWith(function (data) {
        return $filter('number')(data, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('bonus').withTitle('Horas Extras').renderWith(function (data, display, full) {
        var grossSalary = full.grossSalary;
        var bonus = data;
        var total = 0;
        _(bonus).each(function (bond) {
          if (bond.bonus.type == 'percentaje') {
            total += grossSalary * bond.bonus.value / 100;
          }
          ;
          if (bond.bonus.type == 'ammount') {
            total += bond.bonus.value;
          }
          ;
        });
        return $filter('number')(total, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('comisions').withTitle('Comisiones').renderWith(function (data) {
        return $filter('number')(data, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('incomeSum').withTitle('Suma Ingresos').renderWith(function (data, display, full) {
        var grossSalary = full.grossSalary;
        var bonus = full.bonus;
        var total = 0;
        _(bonus).each(function (bond) {
          if (bond.bonus.type == 'percentaje') {
            total += grossSalary * bond.bonus.value / 100;
          }
          ;
          if (bond.bonus.type == 'ammount') {
            total += bond.bonus.value;
          }
          ;
        });
        return $filter('number')(total + grossSalary, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('grossSalary').withTitle('Fondos Reserva').renderWith(function (data) {
        return $filter('number')(data / 12, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('grossSalary').withTitle('IESS Personal').renderWith(function (data) {
        return $filter('number')(data * 9.35 / 100, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('discounts').withTitle('Anticipos').renderWith(function (data, display, full) {
        var grossSalary = full.grossSalary;
        var discounts = data;
        var total = 0;
        _(discounts).each(function (discount) {
          if (discount.discount.type == 'percentaje') {
            total += grossSalary * discount.discount.value / 100;
          }
          ;
          if (discount.discount.type == 'ammount') {
            total += discount.discount.value;
          }
          ;
        });
        return $filter('number')(total, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('expensesSum').withTitle('Suma Gastos').renderWith(function (data, display, full) {
        var grossSalary = full.grossSalary;
        var iess = grossSalary * 9.35 / 100;
        var discounts = full.discounts;
        var total = 0;
        _(discounts).each(function (discount) {
          if (discount.discount.type == 'percentaje') {
            total += grossSalary * discount.discount.value / 100;
          }
          ;
          if (discount.discount.type == 'ammount') {
            total += discount.discount.value;
          }
          ;
        });
        return $filter('number')(total + iess, 2);
      }).withClass('text-right'),
      DTColumnBuilder.newColumn('toPay').withTitle('A Pagar').renderWith(function (data, display, full) {
        var grossSalary = full.grossSalary;
        var iess = grossSalary * 9.35 / 100;
        var discounts = full.discounts;
        var totalExpenses = 0;
        _(discounts).each(function (discount) {
          if (discount.discount.type == 'percentaje') {
            totalExpenses += grossSalary * discount.discount.value / 100;
          }
          ;
          if (discount.discount.type == 'ammount') {
            totalExpenses += discount.discount.value;
          }
          ;
        });
        totalExpenses += iess;
        var grossSalary = full.grossSalary;
        var bonus = full.bonus;
        var totalIncomes = 0;
        _(bonus).each(function (bond) {
          if (bond.bonus.type == 'percentaje') {
            totalIncomes += grossSalary * bond.bonus.value / 100;
          }
          ;
          if (bond.bonus.type == 'ammount') {
            totalIncomes += bond.bonus.value;
          }
          ;
        });
        totalIncomes += grossSalary;
        return $filter('number')(totalIncomes - totalExpenses, 2);
      })
    ];
    $scope.dtOptions = DTOptionsBuilder.newOptions().withOption('ajax', {
      url: 'rolLiquidationForTable',
      type: 'POST',
      date: $scope.filter
    }).withDataProp('data').withOption('serverSide', true).withOption('rowCallback', function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      $('td', nRow).unbind('click');
      $('td', nRow).bind('dblclick', function () {
        $scope.$apply(function () {
          $scope.editCustomer(aData);
        });
      });
      return nRow;
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
        'sFileName': fileName + '.pdf',
        'sPdfOrientation': 'landscape',
        'sPdfSize': 'A4',
        'sTitle': 'Resumen Clientes'
      }
    ]);
    DTInstances.getLast().then(function (dtInstance) {
      tableInstance = dtInstance;
    });
    $scope.datepickerOptions = {
      format: 'yyyy-mm-dd',
      language: 'es',
      autoclose: true
    };
    $scope.reloadData = function () {
      $scope.params = $scope.filter;
      $scope.dtOptions.ajax.data = $scope.params;
      tableInstance.reloadData();
    };
    $scope.maximize = function () {
      if ($('body').hasClass('panel-expand')) {
        $scope.dtOptions.scrollY = $(window).height() * 0.5 * 0.6;
      } else {
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
      tableInstance.rerender();
    };
    $scope.save = function () {
      var finalData = tableInstance.DataTable.data();
      console.log(finalData);
    };
    handlePanelAction();
  }
]);