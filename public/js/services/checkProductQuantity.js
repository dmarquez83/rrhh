'use strict';
angular.module('app').service('checkProductQuantity', [
  function () {
    return {
      'check' : function(products){
        var result = true;
        _(products).each(function(product){
          if (product.quantity == 0) {
            result = false;
          }
        });
        if (!result) {
          toastr.error('No puede ingresar una linea de producto con cantidad 0');
        }
        return result;
      }
    }
  }
]);
