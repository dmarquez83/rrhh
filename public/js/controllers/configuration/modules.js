'use strict';
angular.module('app').controller('ModulesCtrl', [
  '$scope',
  'server',
  function ($scope, server) {
    $scope.init = function () {
      $scope.modulesRoute = 'modules';
      $scope.modules = $scope.modules && $scope.modules.length > 0 ? $scope.modules : [];
      $scope.module = {
        _id: undefined,
        name: undefined,
        route: undefined,
        cssClass: undefined,
        templateUrl: undefined,
        controller: undefined,
        state: undefined,
        isVisible: true,
        submodules: []
      };
      $scope.submodule = undefined;
      $scope.indexModule = 0;
      $scope.indexSubmodule = 0;
    };
    $scope.selectModule = function (index) {
      $scope.indexModule = index;
      $scope.module = _($scope.modules[index]).clone();
      $scope.submodule = {};
    };
    $scope.deleteModule = function (index) {
      if (confirm('Esta seguro de eliminar este elemento?')) {
        var module = $scope.modules[index];
        server.delete($scope.modulesRoute, module._id).success(function (data) {
          if (data) {
            toastr[data.type](data.msg);
            $scope.init();
            getModules();
          }
        });
      }
    };
    $scope.selectSubmodule = function (index) {
      $scope.indexSubmodule = index;
      $scope.submodule = _($scope.module.submodules[index]).clone();
    };
    $scope.addSubmodule = function () {
      $scope.module.submodules.push($scope.submodule);
      $scope.submodule = {};
    };
    $scope.updateSubmodule = function () {
      $scope.module.submodules[$scope.indexSubmodule] = $scope.submodule;
      $scope.submodule = {};
    };
    $scope.deleteSubmodule = function (index) {
      if (confirm('Esta seguro de eliminar este elemento?')) {
        $scope.module.submodules.splice(index, 1);
      }
    };
    $scope.save = function () {
      if ($scope.module._id) {
        server.update($scope.modulesRoute, $scope.module, $scope.module._id).success(function (data) {
          if (data) {
            toastr[data.type](data.msg);
          }
        });
      } else {
        server.save($scope.modulesRoute, $scope.module).success(function (data) {
          if (data) {
            toastr[data.type](data.msg);
          }
        });
      }
      $scope.init();
      getModules();
    };
    function getModules() {
      server.getAll($scope.modulesRoute).success(function (data) {
        if (data) {
          $scope.modules = data;
        }
      });
    }
    $scope.init();
    getModules();
    handlePanelAction();
  }
]);