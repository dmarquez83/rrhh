'use strict';
angular.module('app').controller('FilterSupplierPayCtrl', [
  '$scope',
  '$state',
  'server',
  'transferData',
  function ($scope, $state, server, transferData) {
    
    $scope.selectedCustomers = [];
    $scope.suppliers = [];
    $scope.products = [];
    $scope.salesNumberFrom = [];
    var tableInstance = {};

    $scope.filter = {
      startDate: moment().startOf('month').format('YYYY-MM-DD'),
      endDate: moment().endOf('month').format('YYYY-MM-DD'),
      supplierIds: [],
    };


    $scope.search = function () {
      transferData.data = {};
      transferData.data = $scope.filter;
      $state.go('banks.summarySupplierPay');
    };

    $scope.clean = function () {
      $scope.filter.supplierIds = [];
    };

    handlePanelAction();
  }
]);