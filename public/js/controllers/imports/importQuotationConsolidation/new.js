'use strict';
angular.module('app').controller('NewImportQuotationConsolidationCtrl', [
  '$rootScope',
  '$http',
  '$scope',
  'server',
  'transferData',
  '$timeout',
  function ($rootScope, $http, $scope, server, transferData, $timeout) {

    $scope.isConsolidation = true;
    $scope.importOrder = {};
    $scope.importOrder.status = 'Pendiente de aprobación';
    $scope.importOrder.isConsolidation = true;
    $scope.importOrder.supplier_id = '';
    $scope.importOrder.products = [];
    $scope.importOrder.importQuotationNumbers = [];
    $scope.importOrder.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.suppliers = [];
    $scope.documentDetails = {};
    $scope.disabledDeliveryDate = false;
    $scope.blockActions = {add: true, delete: true};
    $scope.disabledInputs = {code: true, quantity: true, price: false, discount: false};
    $scope.models = {
      selected: null,
      lists: {'products': [], 'selectedProducts': []}
    };

    $http.post('/importQuotation/getImportQuotationSuppliers').success(function(data){
      $scope.suppliers = data;
    });

    var reloadData = function(){
      $rootScope.$broadcast('reloadImportQuotationTable');
    };

    var getImportQuotationBySupplier = function(supplier_id) {
      var parameter = {'parameter': 'products.supplier_id', 'value': supplier_id};
      server.getAllByParameterPostForConsolidation('importQuotation', parameter).success(function(data){
        if (data.length > 0 ){
          $scope.models.lists.products = _(data).map(function(product){
            var newProduct = angular.copy(product);
            var customer = '';
            var businessName = _(product.customer).has('businessName') ? product.customer.businessName : '';
            var comercialName = _(product.customer).has('comercialName') ? product.customer.comercialName : '';
            var personName = _(product.customer).has('names') ? product.customer.names : '';
            var personSurname = _(product.customer).has('surnames') ? product.customer.surnames : '';
            var personCompleteName = personName.trim() + ' ' + personSurname.trim();

            customer = (personCompleteName !== ' ' ? personCompleteName: customer);
            customer = (businessName !== '' ? businessName: customer);
            customer = (comercialName !== '' ? comercialName: customer);
            newProduct.customer = customer;

            return newProduct;
          });
        } else {
          toastr.warning('No se encontró ningún producto');
        }
      });
    };

    var compactProducts = function(filterProducts) {
      var codes = _(filterProducts).pluck('code');
      codes = _(codes).uniq();
      var finalProducts = [];
      _(codes).each(function(code){
        var products = _(filterProducts).where({'code': code});
        var newProduct = angular.copy(products[0]);
        newProduct.quantity = 0;
        _(products).each(function(product){
          newProduct.quantity = math.add(newProduct.quantity, product.quantity);
        });
        finalProducts.push(newProduct);
      });
      return finalProducts;
    };

    var filterProducts = function(){
      var filterProducts = [];
      _($scope.models.lists.selectedProducts).each(function(product){
        if (product.selectedQuantity > 0) {
          var newProduct = angular.copy(product);
          newProduct.quantity = newProduct.selectedQuantity;
          newProduct = _(newProduct).omit(['customer', 'selectedQuantity', 'supplier']);
          filterProducts.push(newProduct);
          $scope.importOrder.importQuotationNumbers.push(product.importQuotationNumber);
          $scope.importOrder.importQuotationNumbers = _($scope.importOrder.importQuotationNumbers).uniq();
        }
      });
      return compactProducts(filterProducts);
    };

    var clean = function(){
      $scope.importOrder = {};
      $scope.importOrder.status = 'Pendiente de aprobación';
      $scope.importOrder.supplier_id = '';
      $scope.importOrder.isConsolidation = true;
      $scope.importOrder.products = [];
      $scope.importOrder.importQuotationNumbers = [];
      $scope.importOrder.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
      $scope.models = {
        selected: null,
        lists: {'products': [], 'selectedProducts': []}
      };
      reloadData();
    };

    var save = function () {
      $http.post('importOrder/saveConsolidation', $scope.importOrder).success(function(data){
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          clean();
        }
      });
    };

    var changeStep = function(event, element){
      if(element.index === 1) {
        $('#wizard > ul > li.previous').show();
        if ($scope.models.lists.selectedProducts.length === 0){
          getImportQuotationBySupplier($scope.importOrder.supplier_id);
        }
      }

      if (element.index === 2) {
        $('#wizard > ul > li.previous').show();
        $scope.importOrder.products = angular.copy(filterProducts());
        $scope.importOrder.selectedProducts = angular.copy($scope.models.lists.selectedProducts);
        $scope.$digest();
        $scope.documentDetails.recalculate();
      }

      if (element.index === 3){
        save();
        $('#wizard > ul > li.previous').hide();
      }
    };

    $scope.models = {
      selected: null,
      lists: {'products': [], 'selectedProducts': []}
    };

    $scope.validateMax = function(index, listName) {
      var product = $scope.models.lists[listName][index];
      var quantityRemaining = product.quantityRemaining || product.quantity;
      if (product.selectedQuantity > quantityRemaining){
        toastr.warning('La cantidad no puede ser mayor a la solicitada');
        product.selectedQuantity = quantityRemaining;
      }
    };

    $timeout(function () {
      $('#wizard').bwizard({nextBtnText: 'Siguiente', backBtnText: 'Anterior',
        clickableSteps: false,
        delay: 100,
        activeIndexChanged: changeStep,
        loop: true,
        validating: function (e, ui) {

          if (ui.index === 0) {
            $('#wizard > ul > li.previous').hide();
            if ($scope.importOrder.supplier_id === '' || $scope.importOrder.supplier_id === undefined){
              toastr.warning('Seleccione un proveedor');
              return false;
            }
          }

          if (ui.index === 1 && ui.nextIndex === 2) {
            $('#wizard > ul > li.previous').show();
            if ($scope.models.lists.selectedProducts.length === 0) {
              toastr.warning('Arrastre al menos un producto');
              return false;
            }
          }

          if (ui.nextIndex === 3){
            $('#wizard > ul > li.next > a').text('Finalizar');
          }

          if (ui.index === 2){
            $('#wizard > ul > li.previous').show();
          }

          if (ui.index === 2 && ui.nextIndex === 3) {
            if ($scope.importOrder.products.length === 0) {
              toastr.warning('No ha ingresado ningun producto');
              return false;
            } else {
              if (!$scope.importOrder.deliveryDate){
                toastr.warning('Ingrese una fecha de entrega');
                return false;
              } else {
                $('#wizard > ul > li.next > a').text('Finalizar');
              }

            }
          }

          if (ui.index === 3 && ui.nextIndex === 0) {
            clean();
            $('#wizard > ul > li.previous').hide();
            $('#wizard > ul > li.next > a').text('Siguiente');
          }

          if (ui.index === 1 && ui.nextIndex === 0) {
            var supplier_id = angular.copy($scope.importOrder.supplier_id);
            clean();
            $scope.importOrder.supplier_id = supplier_id;

          }

        }
      });
      $('#wizard > ul > li.previous').hide();
    }, 100);

  }
]);
