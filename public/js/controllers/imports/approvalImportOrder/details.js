'use strict';
angular.module('app').controller('ApprovalImportQuotationDetailsCtrl', [
  '$scope',
  '$filter',
  '$modalInstance',
  '$modal',
  'selectedData',
  '$http',
  function ($scope, $filter, $modalInstance, $modal, selectedData, $http) {

    $scope.isForAproval = true;
    $scope.importOrder = angular.copy(selectedData.document);
    $scope.actions = {};
    $scope.actions.disabledForm = true;

    $scope.approval = function () {
      $http.put('approvalImportOrder/'+$scope.importOrder._id, $scope.importOrder).success(function (msg) {
        toastr[msg.type](msg.msg);
        $modalInstance.close();
      });
    };

    $scope.rejected = function () {
      $http.put('rejectedImportOrder/'+$scope.importOrder._id, $scope.importOrder).success(function (msg) {
        toastr[msg.type](msg.msg);
        $modalInstance.close();
      });
    };

    $scope.openImportQuotation = function (documentNumber) {
      $modal.open({
        templateUrl: '../../views/imports/importQuotation/details.html',
        controller: 'ImportQuotationDetailsCtrl',
        windowClass: 'xlg',
        resolve: {
          selectedData: function () {
            return {
              'isFromLink': true,
              'documentNumber': documentNumber,
              'documentName': 'importQuotation'
            };
          }
        }
      });
    };

    $scope.close = function () {
      $modalInstance.close();
    };

  }
]);
