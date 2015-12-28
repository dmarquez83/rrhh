'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'fileReader',
  function ($scope, documentValidate, server, fileReader) {

    $scope.massiveArrears = {};
    $scope.massiveArrears.prueba= {datos: 'mi prueba'};


    $scope.openFile = function() {
      alert('entro');
      console.log($scope.massiveArrears.myFile);
      fileReader.readFile($scope.massiveArrears.myFile);
    };



    handlePanelAction();
  }
]);
