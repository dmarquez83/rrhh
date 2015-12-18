'use strict';
angular.module('app').factory('totalDocumentCalculator', [
  '$rootScope',
  function($rootScope){
    return {
      calculate: function(document){
        var documentDiscount = document.documentDiscount ? angular.copy(document.documentDiscount) : 0;
        var productListCopy = angular.copy(document.products);
        var subtotal = 0;
        var subtotalIva = 0;
        var subtotalIvaZero = 0;
        var subtotalWithOutIva = 0;
        var totalDiscount = 0;
        var totalIVA = 0;
        var totalICE = 0;
        var totalIRBPNR = 0;
        var total = 0;
        var ivaTax = {value: 0};
        var iva12Value = 0.12;
        var totalTip = _(document.totals).has('tip') ? angular.copy(document.totals.tip) : 0;

        _(productListCopy).each(function(product) {
          if (product.code !== '') {

            ivaTax = _(product.billing_taxes).findWhere({'taxType_id': $rootScope.IVATaxTypeId});

            if (ivaTax.value > 0) {
              //totalIVA = math.add(totalIVA, product.totalIVA);
              subtotalIva = math.add(subtotalIva, product.subtotal);
            } else if (ivaTax.value === 0) {
              subtotalIvaZero = math.add(subtotalIvaZero, product.subtotal);
            } else {
              subtotalWithOutIva = math.add(subtotalWithOutIva, product.subtotal);
            }

            totalICE = math.add(totalICE, product.totalICE);
            totalIRBPNR = math.add(totalIRBPNR, product.totalIRBPNR);
            totalDiscount = math.add(totalDiscount, product.totalDiscount);
            subtotal = math.add(subtotal, product.subtotal);
          }  

        });

        var finalTotalDocumentDiscount = documentDiscount;
        if (document.discountType === '100') {
          finalTotalDocumentDiscount = math.round(math.multiply(documentDiscount, subtotal), 2);
        }
        
        var documentDiscountValue = documentDiscount;
        if (finalTotalDocumentDiscount > subtotal) {
          toastr.warning('El descuento no puedo ser mayor al total del documento');
          finalTotalDocumentDiscount = subtotal;  
          documentDiscountValue = finalTotalDocumentDiscount;
          if (document.discountType === '100') {
            documentDiscountValue = 1;
          }      
        }

          

        totalDiscount = math.add(totalDiscount, finalTotalDocumentDiscount);
        subtotalIva = math.subtract(subtotalIva, finalTotalDocumentDiscount);
        totalIVA = math.add(totalIVA, math.multiply(subtotalIva, iva12Value));
        total = math.chain(total).add(subtotal).add(totalIVA).add(totalICE).add(totalIRBPNR).done();
        total = math.subtract(total, finalTotalDocumentDiscount);
        total = math.add(total, totalTip);

        return {
          'documentDiscount': documentDiscountValue,
          'subtotal' : math.round(subtotal, 4),
          'subtotalIva': math.round(subtotalIva, 4),
          'subtotalIvaZero': math.round(subtotalIvaZero, 4),
          'subtotalWithOutIva': math.round(subtotalWithOutIva, 4),
          'totalDiscount': math.round(totalDiscount, 4),
          'totalICE': math.round(totalICE, 4),
          'totalIRBPNR': math.round(totalIRBPNR, 4),
          'totalIVA': math.round(totalIVA, 4),
          'tip': math.round(totalTip, 4),
          'total': math.round(total, 4)
        };


      }
    };

  }
]);
