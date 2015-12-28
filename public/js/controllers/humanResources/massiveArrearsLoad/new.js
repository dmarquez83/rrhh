'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'fileReader',
  'XLSXReaderService',
  function ($scope, documentValidate, server, fileReader, XLSXReaderService) {

    $scope.massiveArrears = {};
    $scope.massiveArrears.prueba= {datos: 'mi prueba'};


    $scope.openFile = function() {
      alert('entro');
      console.log($scope.massiveArrears.myFile);
      //fileReader.getReader($scope.massiveArrears.myFile);
    };

    $scope.showPreview = false;
    $scope.showJSONPreview = true;
    $scope.json_string = "";


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
      $scope.json_string = JSON.stringify($scope.sheets[$scope.selectedSheetName], null, 2);
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



   // handlePanelAction();
  }
]);
