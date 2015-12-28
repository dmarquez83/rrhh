'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'XLSXReaderService',
  function ($scope, documentValidate, server, XLSXReaderService) {

    $scope.showPreview = false;
    $scope.showJSONPreview = true;


    $scope.fileChanged = function(files) {
      $scope.isProcessing = true;
      $scope.sheets = [];
      $scope.excelFile = files[0];
      XLSXReaderService.readFile($scope.excelFile, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
        $scope.sheets = xlsxData.sheets;
        $scope.isProcessing = false;
      });
    }

    $scope.updateJSONString = function() {
      console.log($scope.sheets[$scope.selectedSheetName]);
      $scope.json_string = JSON.stringify($scope.sheets[$scope.selectedSheetName], null, 2);
      $scope.prueba = $scope.sheets[$scope.selectedSheetName];
    }

    $scope.showPreviewChanged = function() {
      if ($scope.showPreview) {
        $scope.showJSONPreview = false;
        $scope.isProcessing = true;
        XLSXReaderService.readFile($scope.excelFile, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
          $scope.sheets = xlsxData.sheets;
          $scope.isProcessing = false;
        });
      }
    }

    handlePanelAction();
  }
]);
