'use strict';
angular.module('app').controller('PaymentMethodsCtrl', [
  '$scope',
  '$modalInstance',
  'totalPay',
  'server',
  function ($scope, $modalInstance, totalPay, server) {
    $scope.banks = [];
    var collectTotal = new Big(totalPay.totalPay);
    $scope.paymentMethod = { collectTotal: collectTotal.valueOf() };
    $scope.paymentMethod.pays = [];
    var getBanks = function () {
      server.getAll('bank').success(function (data) {
        $scope.banks = data;
      });
    };
    $scope.sumSelectedTotal = function () {
      var total = new Big(0);
      _($scope.paymentMethod).each(function (method) {
        if (_(method).has('amount')) {
          total = total.plus(method.amount);
        }
      });
      $scope.paymentMethod.selectedTotal = total.valueOf();
      $scope.paymentMethod.pendingTotal = collectTotal.minus(total).valueOf();
    };
    $scope.datepickerOptions = {
      format: 'yyyy-mm-dd',
      language: 'es',
      autoclose: true
    };
    $scope.save = function () {
      server.save('supplierPaymentHistory', $scope.paymentMethod).success(function (data) {
        toastr[data.type](data.msg);
        $modalInstance.close();
      });
    };
    _(totalPay.paysSelected).each(function (data, key) {
      if (data == true) {
        $scope.paymentMethod.pays.push(key);
      }
    });
    getBanks();
  }
]);