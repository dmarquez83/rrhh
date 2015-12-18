'use strict';
angular.module('app').service('checkDocumentActions', [
  function () {
    var salesOffer = {
      'Abierto' : { 'generate' : true, 'update' : true, 'annul': true, warnings: true, disabledForm: false},
      'Pendiente de Aprobación' : { 'generate' : false, 'update' : true, 'annul': true, warnings: true, disabledForm: false},
      'Aprobado' : { 'generate' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true },
      'Rechazado' : { 'generate' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true },
      'Factura generada' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Anulado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true },
      'Pedido de cliente generado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true },
      'Rechazado - Anulado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true }
    };
    var salesOrder = {
      'Abierto' : { 'generate' : true, 'update' : true, 'annul': true, warnings: true, disabledForm: false},
      'Pendiente de Aprobación' : { 'generate' : false, 'update' : true, 'annul': true, warnings: true, disabledForm: false},
      'Aprobado' : { 'generate' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado' : { 'generate' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true},
      'Anulado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Solicitud de compra generada' : { 'generate' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado - Anulado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Recibido completo' : { 'generate' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Recibido parcial' : { 'generate' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Venta parcial' : { 'generate' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Venta completa' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Factura generada' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Facturado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Facturado parcial' : { 'generate' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true}
    };
    var purchaseQuotation = {
      'Abierto' : { 'generate' : true, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Anulado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Pedido parcial' : { 'generate' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Pedido generado' : { 'generate' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true}
    };
    var purchaseOrder = {
      'Abierto' : { 'sendToSupplier' : true, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pendiente de aprobación' : { 'sendToSupplier' : false, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Aprobado' : { 'sendToSupplier' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado' : { 'sendToSupplier' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true},
      'Anulado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Pedido parcial' : { 'sendToSupplier' : true, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Pedido generado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Pedido enviado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Recibido parcial' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Recibido completo' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Rechazado - Anulado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Factura Ingresada' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
    };

    var temporaryStockCustomerInvoice = {
      'Pendiente de Facturar' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Facturado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true}
    };

    var referralGuide = {
      'Pendiente' : { 'sendToDispatch' : false, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Abierto' : { 'sendToDispatch' : true, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pendiente de despacho' : { 'sendToDispatch' : false, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'En transito' : { 'sendToDispatch' : false, 'receivedCustomer' : true,'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Recibido Cliente' : { 'sendToDispatch' : false, 'receivedCustomer' : false,'update' : false, 'annul': false, warnings: false, disabledForm: true},
    };

    var goodsDelivery = {
      'Pendiente' : { 'dispatch' : true, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Recibido Cliente' : { 'dispatch' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
    };

    var customerInvoice = {
      'Abierto' : { 'sendToLogistic' : true, 'update' : false, 'annul': true, warnings: true, disabledForm: false},
      'Pendiente de Aprobación' : { 'sendToLogistic' : false, 'update' : true, 'annul': true, warnings: true, disabledForm: false},
      'Facturado' : { 'sendToLogistic' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Aprobado' : { 'sendToLogistic' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado' : { 'sendToLogistic' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true}
    };

    var importQuotation = {
      'Abierto' : { 'generate' : true, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pendiente de Aprobación' : { 'generate' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true},
      'Aprobado' : { 'generate' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado' : { 'generate' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true},
      'Anulado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Pedido parcial' : { 'generate' : true, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Pedido generado' : { 'generate' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true}
    };

    var importOrder = {
      'Abierto' : { 'sendToSupplier' : true, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pendiente de aprobación' : { 'sendToSupplier' : false, 'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Aprobado' : { 'sendToSupplier' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado' : { 'sendToSupplier' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true},
      'Anulado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Pedido parcial' : { 'sendToSupplier' : true, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Pedido generado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Pedido enviado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Recibido parcial' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Recibido completo' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
      'Rechazado - Anulado' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: true, disabledForm: true},
      'Factura Ingresada' : { 'sendToSupplier' : false, 'update' : false, 'annul': false, warnings: false, disabledForm: true},
    };

    var supplierInvoice = {
      'Abierto' : { 'sendToLogistic' : true, 'update' : false, 'annul': true, warnings: true, disabledForm: false},
      'Pendiente de Aprobación' : { 'sendToLogistic' : false, 'update' : true, 'annul': true, warnings: true, disabledForm: false},
      'Facturado' : { 'sendToLogistic' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Aprobado' : { 'sendToLogistic' : true, 'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Rechazado' : { 'sendToLogistic' : false, 'update' : false, 'annul': true, warnings: true, disabledForm: true}
    };

    var salesRetention = {
      'Pendiente' : {'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pendiente de pago' : {'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pagado' : {'update' : false, 'annul': true, warnings: false, disabledForm: true}
    };

    var purchaseRetention = {
      'Pendiente' : {'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pendiente de pago' : {'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Pagado' : {'update' : false, 'annul': true, warnings: false, disabledForm: true},
      'Enviada' : {'update' : false, 'annul': true, warnings: false, disabledForm: true}
    };

    var goodsReceipt = {
      'Pendiente de entrega' : {'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Ingreso parcial' : {'update' : true, 'annul': true, warnings: false, disabledForm: false},
      'Ingreso completo' : {'update' : false, 'annul': true, warnings: false, disabledForm: true}
    };


    var validateDocument = function(documentName, status) {
      var selectedActions = null;
      switch (documentName) {
        case 'salesOffer':
            selectedActions = salesOffer;
          break;
        case 'salesOrder':
            selectedActions = salesOrder;
          break;
        case 'purchaseQuotation':
            selectedActions = purchaseQuotation;
          break;
        case 'purchaseOrder':
            selectedActions = purchaseOrder;
          break;
        case 'temporaryStockCustomerInvoice':
            selectedActions = temporaryStockCustomerInvoice;
          break;
        case 'referralGuide':
            selectedActions = referralGuide;
          break;
        case 'customerInvoice':
            selectedActions = customerInvoice;
          break;
        case 'goodsDelivery':
            selectedActions = goodsDelivery;
          break;
        case 'importQuotation':
          selectedActions = importQuotation;
          break;
        case 'importOrder':
          selectedActions = importOrder;
          break;
        case 'supplierInvoice':
          selectedActions = supplierInvoice;
          break;
        case 'salesRetention':
          selectedActions = salesRetention;
          break;
        case 'purchaseRetention':
          selectedActions = purchaseRetention;
          break;
        case 'goodsReceipt':
          selectedActions = goodsReceipt;
          break;
      }
      if (status.indexOf('Aprobado') !== -1) {
        return selectedActions.Aprobado;
      }
      if (status.indexOf('Rechazado') !== -1) {
        return selectedActions.Rechazado;
      }

      return angular.copy(selectedActions[status]);
    };

    return validateDocument;
  }
]);
