'use strict';
angular.module('app').service('openDocument', [
  '$modal',
  function ($modal) {

    return {
      open : function (documentName, documentNumber) {

        var templateUrl = '';
        var controller = '';
        var resolve = {selectedData: function(){return {'isFromLink': true, 'documentName': documentName, 'documentNumber': documentNumber};}};

        switch (documentName) {
            case 'salesOffer':
                templateUrl = '../../views/sales/salesOffer/details.html';
                controller = 'SalesOfferDetailsCtrl';
                break;
            case 'salesOrder':
                templateUrl = '../../views/sales/salesOrder/details.html';
                controller = 'SalesOrderDetailsCtrl';
                break;
            case 'customerInvoice':
                templateUrl = '../../views/sales/customerInvoice/details.html';
                controller = 'CustomerInvoiceDetailsCtrl';
                break;
            case 'CustomerInvoice':
                templateUrl = '../../views/sales/customerInvoice/details.html';
                controller = 'CustomerInvoiceDetailsCtrl';
                break;
            case 'SupplierInvoice':
                templateUrl = '../../views/purchase/summaryPurchases/details.html';
                controller = 'PurchaseInvoiceDetailsCtrl';
                break;
            case 'supplierInvoice':
                templateUrl = '../../views/purchase/summaryPurchases/details.html';
                controller = 'PurchaseInvoiceDetailsCtrl';
                break; 
            case 'GoodsReceipt':
                templateUrl = '../../views/purchase/goodsReceipt/details.html';
                controller = 'GoodsReceiptDetailsCtrl';
                break;
            case 'goodsReceipt':
                templateUrl = '../../views/purchase/goodsReceipt/details.html';
                controller = 'GoodsReceiptDetailsCtrl';
                break;          
            case 'payCustomerInvoice':
                templateUrl = '../../views/sales/payCustomerInvoice/details.html';
                controller = 'PayCustomerInvoiceDetailsCtrl';
                break;
            case 'completeCustomerInvoice':
                templateUrl = '../../views/sales/completeCustomerInvoice/details.html';
                controller = 'CompleteCustomerInvoiceDetailsCtrl';
                break;
            case 'purchaseQuotation':
                templateUrl = '../../views/purchase/purchaseQuotation/details.html';
                controller = 'PurchaseQuotationDetailsCtrl';
                break;
            case 'PurchaseQuotation':
                templateUrl = '../../views/purchase/purchaseQuotation/details.html';
                controller = 'PurchaseQuotationDetailsCtrl';
                break;
            case 'purchaseOrder':
                templateUrl = '../../views/purchase/purchaseOrder/details.html';
                controller = 'PurchaseOrderDetailsCtrl';
                break;
            case 'referralGuide':
                templateUrl = '../../views/logistics/referralGuide/details.html';
                controller = 'ReferralGuideDetailsCtrl';
                break;
            case 'importQuotation':
              templateUrl = '../../views/imports/importQuotation/details.html';
              controller = 'ImportQuotationDetailsCtrl';
              break;
            case 'importOrder':
              templateUrl = '../../views/imports/importOrder/details.html';
              controller = 'ImportOrderDetailsCtrl';
              break;
        }

        var modalInstance = $modal.open({
          templateUrl: templateUrl,
          controller: controller,
          windowClass: 'xlg',
          resolve: resolve
        });

      }
    };
  }
]);
