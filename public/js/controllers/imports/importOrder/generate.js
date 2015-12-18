'use strict';
angular.module('app').controller('GenerateImportOrderCtrl', [
  '$scope',
  '$filter',
  '$modalInstance',
  'selectedData',
  '$http',
  'checkProductQuantity',
  function ($scope, $filter, $modalInstance, selectedData, $http, checkProductQuantity) {

    $scope.importOrder = {};
    $scope.documentFromName = selectedData.documentFromName;
    $scope.from = selectedData.from;
    $scope.importOrder = angular.copy(selectedData.document);
    $scope.importOrder.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.importOrder.status = 'Abierto';
    $scope.disabledInputs = {code: true, quantity: false, price: false, discount: false};
    $scope.suppliers = [];
    $scope.blockActions = {add: true, delete: true};
    $scope.specialStock = 'true';
    $scope.specialStockName = 'validateStock';
    $scope.validateStock = 'true';

    $scope.importOrder.products = [];
    _(selectedData.document.products).each(function(product){
      var selectedProduct = {};
      if (_(product).has('quantityRemaining')) {
        if (product.quantityRemaining > 0 ) {
          selectedProduct = angular.copy(product);
          selectedProduct.quantity = product.quantityRemaining;
          selectedProduct.validateStock = product.quantityRemaining;
          $scope.importOrder.products.push(selectedProduct);
        }
      } else {
        selectedProduct = angular.copy(product);
        selectedProduct.validateStock = product.quantity;
        $scope.importOrder.products.push(selectedProduct);
      }
    });

    var masterProducts = angular.copy($scope.importOrder.products);

    var getSuppliers = function() {
      var productsSuppliers = [];
      _($scope.importOrder.products).each(function(product){
        var supplier = angular.copy(product.supplier_id);
        productsSuppliers.push(supplier);
      });
      var parameter = {'value': productsSuppliers};
      $http.post('suppliers/getByProductsIds', parameter).success(function(data){
        $scope.suppliers = data;
      });
    };


    var save = function (){
      $scope.importOrder.documentFromName = selectedData.from;
      $scope.importOrder.documentFromNumber = selectedData.document.number;
      var dataForGenerateImportOrder = {};
      dataForGenerateImportOrder.prevDocument = selectedData.document;
      dataForGenerateImportOrder.importOrder = $scope.importOrder;

      $http.post('generateImportOrder', dataForGenerateImportOrder).success(function(data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $modalInstance.close();
        }
      });
    };

    $scope.save = function (formIsValid) {
      if (formIsValid) {
        if ($scope.importOrder.products.length > 0) {
          if (checkProductQuantity.check($scope.importOrder.products)){
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

    $scope.$watch('importOrder.supplier_id', function(newValue) {
      if(newValue){
        var newProductList = [];
        _(masterProducts).each(function(product){
          if (product.supplier_id === newValue) {
            newProductList.push(product);
          }
        });
        $scope.importOrder.products = angular.copy(newProductList);
      }
    });

    getSuppliers();

  }
]);
