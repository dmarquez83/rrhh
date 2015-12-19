'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {


    handlePanelAction();
  }
]);
