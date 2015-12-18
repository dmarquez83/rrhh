'use strict';
angular.module('app').controller('ImportOrderDetailsCtrl', [
  '$scope',
  '$filter',
  '$modalInstance',
  '$modal',
  'selectedData',
  'server',
  'transferData',
  'SweetAlert',
  '$http',
  'openDocument',
  'checkProductQuantity',
  'checkDocumentActions',
  '$timeout',
  function ($scope, $filter, $modalInstance, $modal, selectedData, server, transferData, SweetAlert, $http,
            openDocument, checkProductQuantity, checkDocumentActions, $timeout) {

    $scope.isFromLink = selectedData.isFromLink;
    $scope.importOrder = {};
    $scope.importOrder.sendEmailToSupplier = true;
    $scope.customers = transferData.customers;
    $scope.disabledDeliveryDate = false;
    $scope.documentDetails = {};
    $scope.actions = {};
    var lastElementFlow = _(transferData.importOrderApprovalFlow).last();
    var masterDocument = {};

    if (_(selectedData).has('document')) {
      $scope.importOrder = angular.copy(selectedData.document);
      $scope.importOrder.sendEmailToSupplier = true;

      $scope.actions = checkDocumentActions('importOrder', $scope.importOrder.status);
      if ($scope.importOrder.documentFromNumber || $scope.importOrder.importQuotationNumbers) {
        $scope.actions.disabledForm = true;
        $scope.actions.update = false;
      }
      $timeout(function () {
        $scope.documentDetails.recalculate();
        masterDocument = angular.copy($scope.importOrder);
      }, 250);
    } else {
      var parameter = {parameter: 'number', value : selectedData.documentNumber};
      server.getByParameterPost(selectedData.documentName, parameter).success(function (data) {
        $scope.importOrder = data;
        $scope.actions.disabledForm = true;
      });
    }

    var generateDocument = function(templateUrl, controller, resolve){
      var generateModal = $modal.open({
        templateUrl: templateUrl,
        controller: controller,
        windowClass: 'xlg',
        resolve: resolve
      });
      generateModal.result.then(function () {
        $modalInstance.close();
      });
    };

    $scope.generateImportOrder = function(){
      var data = {
        'from': 'importOrder', documentFromName: 'Solicitud de Importación',
        'document': angular.copy($scope.importOrder)
      };
      var resolve = {selectedData: function(){return data;}};
      generateDocument('../../views/imports/importOrder/generate.html', 'GenerateImportOrderCtrl', resolve);
    };

    $scope.close = function () {
      $modalInstance.close();
    };

    var update = function () {
      $scope.serverProcess = true;
      server.update('importOrder', $scope.importOrder, $scope.importOrder._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $modalInstance.close();
        }
      });
    };

    $scope.update = function (formIsValid) {
      if(formIsValid) {
        if ($scope.importOrder.products.length > 0) {
          if (checkProductQuantity.check($scope.importOrder.products)){
            update();
          }
        } else {
          toastr.error('No ha ingresado ningún producto');
        }

      } else {
        toastr.warning('Revisa errores en el formulario');
      }
    };

    $scope.openImportQuotation = function (documentNumber) {
      $modal.open({
        templateUrl: '../../views/imports/importQuotation/details.html',
        controller: 'ImportQuotationDetailsCtrl',
        windowClass: 'xlg',
        resolve: {
          selectedData: function () {
            return {
              'isFromLink': true,
              'documentNumber': documentNumber,
              'documentName': 'importQuotation'
            };
          }
        }
      });
    };

    var sendImportOrderToSupplier = function() {
      $scope.serverProcess = true;
      $http.post('goodsReceipt/storeFromImportOrder', $scope.importOrder).success(function(data){
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $modalInstance.close();
        }
      });
    };

    $scope.sendImportOrderToSupplier = function(){
      if (_($scope.importOrder).isEqual(masterDocument)){
        sendImportOrderToSupplier();
      } else {
        SweetAlert.swal('Espera...', 'Ha realizado cambios en el documento, debes guardarlos primero', 'warning');
      }
    };

    $scope.readyForSend = function() {
      if(lastElementFlow !== null && lastElementFlow !== undefined){
        var approvalStatus = lastElementFlow.approvalStatus;
        if ($scope.importOrder.status === approvalStatus || $scope.importOrder.status === 'Aprobado') {
          return true;
        }
      } else {
        if ($scope.importOrder.status === 'Abierto' || $scope.importOrder.status === 'Aprobado') {
          return true;
        }
      }
      return false;
    };

    $scope.openDocument = function(){
      openDocument.open($scope.importOrder.documentToName, $scope.importOrder.documentToNumber);
    };

    $scope.openFromDocument = function(){
      console.log($scope.importOrder.documentFromName);
      console.log($scope.importOrder.documentFromNumber);
      openDocument.open($scope.importOrder.documentFromName, $scope.importOrder.documentFromNumber);
    };

    $scope.annul = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: 'Está seguro de anular esta solicitud?',
          text: 'Si anula este registro no podrá revertir el proceso',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#DD6B55', confirmButtonText: 'Si, anular',
          cancelButtonText: 'No, cancelar',
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('importOrder', $scope.importOrder._id).success(function(result){
              $scope.serverProcess = false;
              if(result.type === 'success') {
                SweetAlert.swal('Anulado!', result.msg, result.type);
                $modalInstance.close();
              } else {
                SweetAlert.swal('Error!', result.msg, result.type);
              }
            });
          }
        });
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
        $timeout(function () {
          $scope.documentDetails.recalculate();
        }, 300);

      }
    });


  }
]);
