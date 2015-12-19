'use strict';
angular.module('app').controller('MassiveBonusDiscountLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {


    handlePanelAction();
  }
]);