'use strict';

angular.module('app').controller('PreviewController','fileReader', function($scope, fileReader) {
    $scope.showPreview = false;

   /* $scope.readFile = function(files) {
       alert('entro');
    };
*/
    /*$scope.showPreviewChanged = function() {
        if ($scope.showPreview) {
            XLSXReaderService.readFile($scope.excelFile, $scope.showPreview).then(function(xlsxData) {
                $scope.sheets = xlsxData.sheets;
            });
        };
    };*/
});
