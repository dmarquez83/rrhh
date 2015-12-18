'use strict';
angular.module('app').controller('NewImportQuotationCtrl', [
  '$rootScope',
  '$scope',
  'server',
  'checkProductQuantity',
  'checkDocumentActions',
  function ($rootScope, $scope, server, checkProductQuantity, checkDocumentActions) {

    $scope.serverProcess = false;
    $scope.importQuotation = {};
    $scope.importQuotation.status = 'Abierto';
    $scope.importQuotation.products = [];
    $scope.importQuotation.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
    $scope.importQuotation.seller_id = USER_INFO.employee._id;
    $scope.sellerName = USER_INFO.employee.names + ' ' + USER_INFO.employee.surnames;
    $scope.customers = [];
    $scope.documentDetails = {};
    $scope.actions = checkDocumentActions('importQuotation', $scope.importQuotation.status);

    var reloadData = function(){
      $rootScope.$broadcast('reloadImportQuotationTable');
    };

    var save = function() {
      $scope.serverProcess = true;
      server.save('importQuotation', $scope.importQuotation).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.clean();
        }
      });
    };

    $scope.save = function (formIsValid) {
      if(formIsValid){
        if ($scope.importQuotation.products.length > 0) {
          if (checkProductQuantity.check($scope.importQuotation.products)){
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
      $scope.importQuotation = {};
      $scope.importQuotation = { status: 'Abierto' };
      $scope.importQuotation.products = [];
      $scope.importQuotation.creationDate = moment().format('YYYY-MM-DD HH:mm:ss');
      $scope.importQuotation.seller_id = USER_INFO.employee._id;
      $scope.sellerName = USER_INFO.employee.names + ' ' + USER_INFO.employee.surnames;
      $scope.importQuotationForm.$setPristine();
      reloadData();

    };

    handlePanelAction();

  }
]);
