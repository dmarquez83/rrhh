'use strict';
angular.module('app').controller('HumanResourcesConfigurationCtrl', [
  '$scope',
  'server',
  function ($scope, server) {

    $scope.configuration = {fourteenthSalary:{}, thirteenthSalary:{}};
    $scope.serverProcess = false;
    $scope.isUpdate = false;

    var getConfiguration = function () {
      server.getAll('humanResourcesConfiguration').success(function (data) {
        $scope.configuration = data;
      });
    };

    $scope.save = function () {
      $scope.serverProcess = true;
        server.save('humanResourcesConfiguration', $scope.configuration).success(function (data) {
          toastr[data.type](data.msg);
          $scope.serverProcess = false;
        });
        getConfiguration();
    };

    $scope.datepickerOptions = {
      format: 'yyyy-mm-dd',
      language: 'es',
      autoclose: true
    };

    $scope.openThirteenthSalaryEndDate = function($event) {
      $event.preventDefault();
      $event.stopPropagation();
      $scope.thirteenthSalaryEndDateOpened = true;
    };

    getConfiguration();
    handlePanelAction();
  }
]);