'use strict';
angular.module('app').controller('ConfigurationCtrl', [
  '$scope',
  'progress',
  'server',
  function ($scope, progress, server) {
    $scope.configuration = {};
    $scope.configuration.invoicing = {};
    $scope.configuration.purchasing = {};
    $scope.configuration.retainerIncomeTaxes = {};
    $scope.configuration.payableIncomeTaxes = {};
    $scope.assets = [];
    $scope.liabilities = [];
    $scope.salesCost = [];
    $scope.salesRevenue = [];
    $scope.incomeTaxTable = [];
    var isSet = false;
    server.getAll('statement').success(function (data) {
      $scope.assets = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'CORRIENTE',
          'ACTIVO'
        ]);
      });
      $scope.liabilities = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'CORRIENTE O CORTO PLAZO',
          'PASIVO'
        ]);
      });
      $scope.salesCost = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'GASTOS-EGRESOS',
          'COSTO DE VENTA'
        ]);
      });
      $scope.salesRevenue = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'OPERATIVAS',
          'RENTAS-INGRESOS'
        ]);
      });
      $scope.receivableIRFTaxesAccounts = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'IRF Por Cobrar',
          'CORRIENTE',
          'ACTIVO'
        ]);
      });
      $scope.payableIRFTaxesAccounts = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'IRF por pagar',
          'CORRIENTE O CORTO PLAZO',
          'PASIVO'
        ]);
      });
      $scope.retainerIvaAccounts = _(data).filter(function (ledgerAccounts) {
        console.log(ledgerAccounts.ancestors);
        return _(ledgerAccounts.ancestors).isEqual([
          'IVA Retenido por cobrar',
          'CORRIENTE',
          'ACTIVO'
        ]);
      });
      $scope.payableIvaAccounts = _(data).filter(function (ledgerAccounts) {
        return _(ledgerAccounts.ancestors).isEqual([
          'IVA retenido por pagar',
          'CORRIENTE O CORTO PLAZO',
          'PASIVO'
        ]);
      });
    });
    server.getAll('incomeTaxTable').success(function (data) {
      $scope.incomeIRFTaxTable = _(data).where({ Type: 'IRF' });
      $scope.incomeIVATaxTable = _(data).where({ Type: 'IVA' });
    });
    server.getAll('configuration').success(function (data) {
      if (data.length == 1) {
        $scope.configuration = data[0];
        isSet = true;
      }
    });
    $scope.save = function () {
      var newConfiguration = {};
      if (isSet) {
        newConfiguration = {
          configuration: $scope.configuration,
          action: 'update'
        };
      } else {
        newConfiguration = {
          configuration: $scope.configuration,
          action: 'save'
        };
      }
      server.save('configuration', newConfiguration).success(function (data) {
        isSet = true;
      });
    };
    $scope.automaticaSelection = function () {
      $scope.configuration.receivableIRFTaxes = {};
      $scope.configuration.payableIRFTaxes = {};
      _($scope.incomeIRFTaxTable).each(function (tax, key) {
        var retainerAccount = _($scope.receivableIRFTaxesAccounts).filter(function (account) {
            var numeroPorcentaje = tax.Porcentaje.replace(/\D/g, '');
            var porcentaje = 0;
            _(numeroPorcentaje).each(function (character) {
              if ($.isNumeric(character)) {
                porcentaje = character;
              }
            });
            return account.name.indexOf(porcentaje) > 0;
          });
        var payableAccount = _($scope.payableIRFTaxesAccounts).filter(function (account) {
            var numeroPorcentaje = tax.Porcentaje.replace(/\D/g, '');
            var porcentaje = 0;
            _(numeroPorcentaje).each(function (character) {
              if ($.isNumeric(character)) {
                porcentaje = character;
              }
            });
            return account.name.indexOf(porcentaje) > 0;
          });
        var ledgerRetainer = {}, payableLedger = {};
        ledgerRetainer[tax._id] = retainerAccount[0];
        payableLedger[tax._id] = payableAccount[0];
        _($scope.configuration.receivableIRFTaxes).extend(ledgerRetainer);
        _($scope.configuration.payableIRFTaxes).extend(payableLedger);
      });
    };
    progress.complete();
  }
]);