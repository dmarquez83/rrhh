'use strict';
angular.module('app').controller('NewImportCtrl', [
  '$rootScope',
  '$scope',
  '$modal',
  'server',
  function ($rootScope, $scope, $modal, server) {

    $scope.serverProcess = false;
    $scope.import = {};
    $scope.import.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.import.importOrders = [''];

    $scope.openImportOrderModal = function () {
      var modalInstance = $modal.open({
        templateUrl: '../../views/imports/importOrder/importOrdersModal.html',
        controller: 'ImportOrdersModalCtrl',
        windowClass: 'xlg',
        resolve: {
          selectedData: function () {
            return {
              'isFromLink': true,
              'documentName': 'importOrder'};
          }
        }
      });
      modalInstance.result.then(function () {
        reloadData();
      });
    };


    handlePanelAction();

  }
]);
