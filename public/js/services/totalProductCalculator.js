'use strict';
angular.module('app').factory('totalProductCalculator', ['$rootScope', 'transferData',
  function($rootScope){
    return {
      calculate: function(selectedProduct){
        var product = angular.copy(selectedProduct);
        var quantity = product.quantity === null || product.quantity === undefined ? 0 : product.quantity;
        var discount = product.discount === null || product.discount === undefined ? 0 : product.discount;
        var price = product.price;
        var discountTotal = 0;

        if (product.discountType === '100') {
          if (discount > 1) {
            product.discount = 1;
            discount = 1;
            toastr.warning('El descuento no puede exceder el 100 %');
          }
          discountTotal = math.multiply(price, discount);
          discountTotal = math.round(discountTotal, 2);
        }

        if (product.discountType === '1') {
          if (discount > price) {
            product.discount = price;
            discount = price;
            toastr.warning('El descuento no puede ser mayor al Precio');
          }
          discountTotal = discount;
        }


        var ivaTax = _(selectedProduct.billing_taxes).findWhere({'taxType_id': $rootScope.IVATaxTypeId});
        var ivaPercentage = ivaTax === null || ivaTax === undefined ? 0 : ivaTax.value;

        var iceTax = _(selectedProduct.billing_taxes).findWhere({'taxType_id': $rootScope.ICETaxTypeId});
        var icePercentage = iceTax === null || iceTax === undefined ? 0: iceTax.value;

        var irbpnrTax = _(selectedProduct.billing_taxes).findWhere({'taxType_id': $rootScope.IRBPNRTaxTypeId});
        var irbpnrTaxPercentage = irbpnrTax === null || irbpnrTax === undefined ? 0: irbpnrTax.value;


        product.totalUnitPrice = math.multiply(price, quantity);
        product.totalDiscount = math.multiply(discountTotal, quantity);
        product.subtotal = math.round(math.subtract(product.totalUnitPrice, product.totalDiscount), 4);
        product.unitSubtotal = math.round(math.subtract(price, discountTotal), 4);
        product.totalIVA = math.round(math.multiply(product.subtotal, ivaPercentage), 4);
        product.totalICE = math.round(math.multiply(product.subtotal, icePercentage), 4);
        product.totalIRBPNR = math.round(math.multiply(product.subtotal, irbpnrTaxPercentage), 4);
        product.total = math.round(math.chain(product.subtotal).add(product.totalIVA).add(product.totalICE).add(product.totalIRBPNR).done(), 4);

        return angular.copy(product);
      }
    };

  }
]);
