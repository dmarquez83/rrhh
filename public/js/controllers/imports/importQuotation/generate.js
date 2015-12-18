'use strict';
angular.module('app').controller('GenerateImportQuotationCtrl', [
  '$scope',
  '$filter',
  '$modalInstance',
  'selectedData',
  'server',
  'transferData',
  'checkProductQuantity',
  '$timeout',
  function ($scope, $filter, $modalInstance, selectedData, server, transferData, checkProductQuantity, $timeout) {

    $scope.importQuotation = {};
    $scope.customers = {};
    $scope.importQuotation = angular.copy(selectedData.document);
    _($scope.importQuotation.products).each(function(product, key){
      $scope.importQuotation.products[key].discountType = null;
      $scope.importQuotation.products[key].discount = 0;
    });
    $scope.actions = {};
    $scope.actions.disabledForm = true;
    $scope.disabledComment = false;
    $scope.disabledRequiredDate = false;
    $scope.importQuotation.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.importQuotation.status = 'Abierto';
    $scope.documentFromName = selectedData.documentFromName;
    $scope.from = selectedData.from;
    $scope.documentDetails = {};

    var save = function() {
      $scope.serverProcess = true;
      $scope.importQuotation.modelFromName = selectedData.model;
      $scope.importQuotation.documentFromName = selectedData.from;
      $scope.importQuotation.documentFromNumber = selectedData.document.number;
      server.save('importQuotation/generateFromSalesOrder', $scope.importQuotation).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.close();
        }
      });
    };

    $scope.save = function (formIsValid) {
      if(formIsValid){
        if ($scope.importQuotation.products.length > 0) {
          if (checkProductQuantity.check($scope.importQuotation.products)){
            save();
          }
        } else {
          toastr.error('No ha ingresado ningún producto');
        }
      } else {
        toastr.warning('Revisar errores en el formulario');
      }
    };


    $scope.close = function () {
      $modalInstance.close();
    };

    $timeout(function () {
      $scope.documentDetails.recalculate();
    }, 100);

  }
]);
