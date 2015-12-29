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
    var countsheets = 0;


    $scope.fileChanged = function(files) {
      $scope.isProcessing = true;
      $scope.sheets = [];
      $scope.excelFile = files[0];
      XLSXReaderService.readFile($scope.excelFile, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
        $scope.sheets = xlsxData.sheets;
        $scope.isProcessing = false;
        countsheets = (Object.keys($scope.sheets).length);
        if(countsheets > 1){
          toastr.error('Error', 'El archivo Excel tiene mas de 1 Hoja de trabajo');
        }else{
          angular.forEach($scope.sheets, function (sheetData, sheetName) {
            $scope.datos = sheetData;
          });
        }

      });
    }

    $scope.enviar = function(){

     /* angular.forEach($scope.sheets, function (sheetData, sheetName) {
        angular.forEach(sheetData, function (row) {
          console.log(row.Código,row.Fecha,row.Hora);
        });
      });*/

    }

    handlePanelAction();
  }
]);
