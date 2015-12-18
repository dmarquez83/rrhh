'use strict';
angular.module('app').controller('PaymentMethodsCtrl', [
  '$scope',
  '$modalInstance',
  'totalCollect',
  'server',
  function ($scope, $modalInstance, totalCollect, server) {
    $scope.banks = [];
    $scope.paymentMethod = { collectTotal: totalCollect.totalCollect };
    $scope.paymentMethod.quotas = [];

    var getBanks = function () {
      server.getAll('bank').success(function (data) {
        $scope.banks = data;
      });
    };

    $scope.sumSelectedTotal = function () {
      var total = 0;
      _($scope.paymentMethod).each(function (method) {
        if (_(method).has('amount')) {
          total += parseFloat(method.amount);
        }
      });
      $scope.paymentMethod.selectedTotal = total;
      $scope.paymentMethod.pendingTotal = totalCollect.totalCollect - total;
    };

    $scope.datepickerOptions = {
      format: 'yyyy-mm-dd',
      language: 'es',
      autoclose: true
    };

    $scope.save = function () {
      server.save('customerPaymentHistory', $scope.paymentMethod).success(function (data) {
        toastr[data.type](data.msg);
        $modalInstance.close();
      });
    };

    _(totalCollect.quotasSelected).each(function (data, key) {
      if (data == true) {
        $scope.paymentMethod.quotas.push(key);
      }
    });
    getBanks();
  }
]);