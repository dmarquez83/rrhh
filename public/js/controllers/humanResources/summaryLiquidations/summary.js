'use strict';
angular.module('app').controller('SummaryLiquidationsCtrl', [
  '$scope',
  'server',
  function ($scope, server) {


    handlePanelAction();
  }
]);