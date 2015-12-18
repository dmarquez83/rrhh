'user stric';
angular.module('app').config([
  '$stateProvider',
  '$urlRouterProvider',
  function ($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise('/dashboard/resumenGeneral');
    _(MODULES).each(function (actualModule, indexModule, modulesArray) {
      if (actualModule.submodules) {
        _(actualModule.submodules).each(function (submodule, indexSubmodule, submodulesArray) {
          $stateProvider.state(actualModule.state + '.' + submodule.state, {
            url: '/' + submodule.route,
            templateUrl: submodule.templateUrl ? submodule.templateUrl : undefined,
            controller: submodule.controller ? submodule.controller + 'Ctrl' : undefined
          });
        });
      }
      $stateProvider.state(actualModule.state, {
        abstract: actualModule.submodules && actualModule.submodules.length > 0 ? true : false,
        url: '/' + actualModule.route,
        templateUrl: actualModule.templateUrl,
        controller: actualModule.controller ? actualModule.controller + 'Ctrl' : undefined
      });
    });

    $stateProvider.state('otherwise', { url: '/dashboard/resumenGeneral' });
  }
]).run([
  '$rootScope',
  '$state',
  function ($rootScope, $state) {
    $rootScope.$state = $state;
  }
]);
