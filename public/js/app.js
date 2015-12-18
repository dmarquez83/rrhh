'use strict';
angular.module('app', [
  'ngSanitize',
  'ui.router',
  'ngProgress',
  'ui.utils',
  'datatables',
  'ui.bootstrap',
  'ngTable',
  'selectize',
  'chart.js',
  'ngImageInputWithPreview',
  'ui.checkbox',
  'oitozero.ngSweetAlert',
  'ui.utils.masks',
  'dndLists',
  'base64',
  'ui.select'
])
.run([
  '$rootScope',
  'ngProgress',
  'DTDefaultOptions',
  '$http',
  'server',
  '$modal',
  function ($rootScope, ngProgress, DTDefaultOptions, $http, server, $modal) {

    DTDefaultOptions.setLanguageSource('custom_components/datatables/Spanish.json');

    $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
      ngProgress.color('#14ADE3');
      ngProgress.height('3px');
      ngProgress.start();
    });
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
      ngProgress.color('#14ADE3');
      ngProgress.complete();
    });

    moment.locale('es');

    $http.defaults.headers.common['X-CSRF-TOKEN'] = CSRF_TOKEN;

    $http.post('generalParameters/configParameters').success(function(data){
      $rootScope.IVATaxTypeId = data.IVATaxTypeId;
      $rootScope.ICETaxTypeId = data.ICETaxTypeId;
      $rootScope.IRBPNRTaxTypeId = data.IRBPNRTaxTypeId;
      $rootScope.SAEBASIC = data.SAEBASIC == "1";
      $rootScope.SAEACCOUNTING = data.SAEACCOUNTING == "1";
      $rootScope.SAEINVENTORY = data.SAEINVENTORY == "1";
      $rootScope.PAYMENTWAYS = data.PAYMENTWAYS;
      $rootScope.PAYMENTMETHODS = data.PAYMENTMETHODS;
      $rootScope.FEAMBIENTE = data.FEAMBIENTE;
      $rootScope.FEEMISION = data.FEEMISION;
      $rootScope.COMPANYSERIE = data.COMPANYSERIE;
      $rootScope.WAREHOUSESERIE = data.WAREHOUSESERIE;
    });

    if (COMPANY_INFORMATION === null && USER_INFO.systemUser.username !== 'root') {
      var modal = $modal.open({
          templateUrl: '../../views/initialWizard/new.html',
          controller: 'InitialWizardCtrl',
          windowClass: 'xlg',
          backdrop : 'static'
      });
      modal.result.then(function () {
        window.location = "/login";
      });
    };

    function refreshToken() {
      $.ajax({
          url: "/customers",
          type: "POST",
          data: { CSRF: CSRF_TOKEN},
          error: function (result) {
              window.location = "/logout";
          },
          success: function () {
              setInterval(refreshToken, 7200000);
          }
      });
    };

    setInterval(refreshToken, 7200000);
    //TOUR.start();


  }
])
.config([
  '$httpProvider',
  function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = CSRF_TOKEN;
  }
]);

angular.module('app').filter('propsFilter', function() {
  return function(items, props) {
    var out = [];

    if (angular.isArray(items)) {
      items.forEach(function(item) {
        var itemMatches = false;

        var keys = Object.keys(props);
        for (var i = 0; i < keys.length; i++) {
          var prop = keys[i];
          var text = props[prop].toLowerCase();
          if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
            itemMatches = true;
            break;
          }
        }

        if (itemMatches) {
          out.push(item);
        }
      });
    } else {
      // Let the output be the input untouched
      out = items;
    }

    return out;
  }
});
