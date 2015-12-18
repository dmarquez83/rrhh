'use strict';
angular.module('app').controller('CompanyInfoCtrl', [
  '$scope',
  '$window',
  'documentValidate',
  'server',
  function ($scope, $window, documentValidate, server) {
    $scope.serverProcess = false;
    $scope.isCreated = false;
    $scope.companyInfo = {};
    $scope.companyInfo.identification = '';

    server.getAll('companyInfo').success(function (data) {
      if (data != '') {
        $scope.companyInfo = data;
        $scope.isCreated = true;
      }
    });
    $scope.save = function (formIsValid) {
      if(formIsValid) {
        $scope.serverProcess = true;
        if ($scope.isCreated) {
          server.update('companyInfo', $scope.companyInfo, $scope.companyInfo._id).success(function (data) {
            $scope.serverProcess = false;
            toastr[data.type](data.msg);
          });
        } else {
          server.save('companyInfo', $scope.companyInfo).success(function (data) {
            $scope.serverProcess = false;
            toastr[data.type](data.msg);
          });
        }
      } else {
        toastr.warning('Revisar errores en el formulario');
      }

    };

    $scope.clean = function () {
      $scope.isCreated = false;
      $scope.companyInfo = {};
      $scope.companyInfo.specialContributor = false;
      $scope.companyInfo.accountingForced = false;
    };

    $scope.validateIdentification = function () {
      if ($scope.companyInfo.identification != '' && $scope.companyInfo.identification != undefined) {
        var isValidate = documentValidate.validateDocument($scope.companyInfo.identification);
        if (!isValidate) {
          $scope.companyInfo.identification = '';
        }
      } else {
        $scope.companyInfo.identification = null;
      }
    };
    handlePanelAction();
  }
]);