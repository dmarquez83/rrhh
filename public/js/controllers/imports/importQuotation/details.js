'use strict';
angular.module('app').controller('ImportQuotationDetailsCtrl', [
  '$scope',
  '$filter',
  '$modalInstance',
  '$modal',
  'selectedData',
  'server',
  'transferData',
  'SweetAlert',
  'openDocument',
  'checkProductQuantity',
  '$timeout',
  'checkDocumentActions',
  function ($scope, $filter, $modalInstance, $modal, selectedData, server, transferData, SweetAlert, openDocument,
            checkProductQuantity, $timeout, checkDocumentActions) {

    $scope.isFromLink = selectedData.isFromLink;
    $scope.importQuotation = {};
    $scope.importQuotation.supplierId = [];
    $scope.customers = transferData.customers;
    $scope.documentDetails = {};
    $scope.actions = {};
    $scope.showDistributionQuantity = 'true';
    var masterDocument = {};

    if (_(selectedData).has('document')) {
      $scope.importQuotation = angular.copy(selectedData.document);
      $scope.actions = checkDocumentActions('importQuotation', $scope.importQuotation.status);
      if ($scope.importQuotation.documentFromNumber) {
        $scope.actions.disabledForm = true;
        $scope.actions.update = false;
      }
      if ($scope.actions.disabledForm === true) {
        $scope.disabledComment = true;
        $scope.disabledRequiredDate = true;
      }
      $timeout(function () {
        $scope.documentDetails.recalculate();
        masterDocument = angular.copy($scope.importQuotation);
      }, 100);
    } else {
      var parameter = {parameter: 'number', value : selectedData.documentNumber};
      server.getByParameterPost(selectedData.documentName, parameter).success(function (data) {
        $scope.importQuotation = data;
        $scope.actions.disabledForm = true;
      });
    }

    var generateDocument = function(templateUrl, controller, resolve){
      if (_($scope.importQuotation).isEqual(masterDocument)){
        var generateModal = $modal.open({
          templateUrl: templateUrl,
          controller: controller,
          windowClass: 'xlg',
          resolve: resolve
        });
        generateModal.result.then(function () {
          $modalInstance.close();
        });
      } else {
        SweetAlert.swal('Espera...', 'Ha realizado cambios en el documento, debes guardarlos primero', 'warning');
      }
    };

    $scope.generateImportOrder = function(){
      var data = {
        'from': 'importQuotation', documentFromName: 'Solicitud de Importación',
        'document': angular.copy($scope.importQuotation)
      };
      var resolve = {selectedData: function(){return data;}};
      generateDocument('../../views/imports/importOrder/generate.html', 'GenerateImportOrderCtrl', resolve);
    };

    $scope.close = function () {
      $modalInstance.close();
    };

    $scope.openDocument = function(){
      openDocument.open($scope.importQuotation.documentToName, $scope.importQuotation.documentToNumber);
    };

    $scope.openFromDocument = function(){
      openDocument.open($scope.importQuotation.documentFromName, $scope.importQuotation.documentFromNumber);
    };

    var update = function() {
      $scope.serverProcess = true;
      server.update('importQuotation', $scope.importQuotation, $scope.importQuotation._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $modalInstance.close();
        }
      });
    };

    $scope.update = function (formIsValid) {
      if(formIsValid){
        if ($scope.importQuotation.products.length > 0) {
          if (checkProductQuantity.check($scope.importQuotation.products)){
            update();
          }
        } else {
          toastr.error('No ha ingresado ningún producto');
        }
      } else {
        toastr.warning('Revisar errores en el formulario');
      }
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
            server.delete('importQuotation', $scope.importQuotation._id).success(function(result){
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

  }
]);
