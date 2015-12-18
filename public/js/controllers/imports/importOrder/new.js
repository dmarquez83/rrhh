'use strict';
angular.module('app').controller('NewImportOrderCtrl', [
  '$rootScope',
  '$scope',
  'server',
  'checkProductQuantity',
  'checkDocumentActions',
  function ($rootScope, $scope, server, checkProductQuantity, checkDocumentActions) {

    $scope.serverProcess = false;
    $scope.importOrder = {};
    $scope.importOrder.status = 'Abierto';
    $scope.importOrder.isConsolidation = false;
    $scope.importOrder.products = [];
    $scope.importOrder.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.importOrder.seller_id = USER_INFO.employee._id;
    $scope.sellerName = USER_INFO.employee.names + ' ' + USER_INFO.employee.surnames;
    $scope.disabledDeliveryDate = false;
    $scope.documentDetails = {};
    $scope.actions = checkDocumentActions('importOrder', $scope.importOrder.status);
    $scope.specialProductsSupplier = null;

    var reloadData = function(){
      $rootScope.$broadcast('reloadImportOrderTable');
    };

    var save = function(){
      $scope.serverProcess = true;
      server.save('importOrder', $scope.importOrder).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.clean();
        }
      });
    };

    $scope.save = function (formIsValid) {
      if(formIsValid){
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

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.importOrder = {};
      $scope.importOrder.status = 'Abierto';
      $scope.importOrder.isConsolidation = false;
      $scope.importOrder.products = [];
      $scope.importOrder.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
      $scope.importOrder.seller_id = USER_INFO.employee._id;
      $scope.sellerName = USER_INFO.employee.names + ' ' + USER_INFO.employee.surnames;
      reloadData();
      $scope.importOrderForm.$setPristine();
    };

    $scope.$watch('importOrder.supplier_id', function(newValue) {
      if (newValue) {
        $scope.specialProductsSupplier = newValue;
        var finalProducts = [];
        _($scope.importOrder.products).each(function(product){
          if (product.supplier_id === newValue) {
            finalProducts.push(product);
          }
        });
        $scope.importOrder.products = angular.copy(finalProducts);
        $scope.documentDetails.recalculate();
      }
    });


    handlePanelAction();

  }
]);
