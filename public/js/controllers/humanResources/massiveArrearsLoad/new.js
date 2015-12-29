'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'XLSXReaderService',
  function ($scope, documentValidate, server, XLSXReaderService) {

    $scope.showPreview = false;
    $scope.showJSONPreview = true;
    $scope.datos = {};


    $scope.fileChanged = function(files) {
      $scope.isProcessing = true;
      $scope.sheets = [];
      $scope.excelFile = files[0];
      XLSXReaderService.readFile($scope.excelFile, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
        $scope.sheets = xlsxData.sheets;
        $scope.isProcessing = false;
        angular.forEach($scope.sheets, function (sheetData, sheetName) {
          $scope.datos = sheetData;
        });
      });
    }

    $scope.enviar = function(){

      //alert('enviar');
      //console.log($scope.sheets);
      //console.log($scope.sheets.length);

      angular.forEach($scope.sheets, function (sheetData, sheetName) { /*este ciclo lleva la cantidad de hojas*/

        //console.log(sheetName);//nombre de la hola

        $scope.datos = sheetData;

        /*angular.forEach(sheetData, function (row) {

          console.log(row.Código,row.Fecha,row.Hora);

        });*/

      });
    }

    handlePanelAction();
  }
]);
