'use strict';
angular.module('app').controller('DocumentsConfigurationCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {

    handlePanelAction();
    $scope.newDocument = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getDocuments = function () {
      server.getAll('documentsConfiguration').success(function (data) {
        $scope.documents = data;
      });
    };

    $scope.save = function () {
      $scope.serverProcess = true;
      if ($scope.isUpdate) {
        server.update('documentsConfiguration', $scope.newDocument, $scope.newDocument._id).success(function (data) {
          $scope.serverProcess = false;
          toastr.success(data);
          $scope.isUpdate = false;
          getDocuments();
          $scope.newDocument = {};
        });
      } else {
        server.save('documentsConfiguration', $scope.newDocument).success(function (data) {
          $scope.serverProcess = false;
          toastr.success(data);
          getDocuments();
          $scope.newDocument = {};
        });
      }
    };

    $scope.reset = function () {
      $scope.isUpdate = false;
      $scope.newDocument = {};
    };

    $scope.selectDocument = function (selectedDocument) {
      $scope.newDocument = selectedDocument;
      $scope.isUpdate = true;
    };
    
    getDocuments();
  }
]);