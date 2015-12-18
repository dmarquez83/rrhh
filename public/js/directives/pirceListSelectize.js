'user strict';
angular.module('app').directive('selectizePriceList', function() {
  return {
    restrict: 'EA',
    require: '^ngModel',
    scope: {
      ngModel: '=',
      ngRequired: '@',
      id: '@',
      name: '@',
      tabindex: '@',
      priceListData: '=',
      ngDisabled: '=',
      ngChange: '=',
      multiple: '@'
    },
    templateUrl: '../../views/products/selectizePricesList.html',
    controller: [
    '$scope', 'server', 'transferData', '$timeout',
    function($scope, server, transferData, $timeout) {
      var maxItems = $scope.multiple === 'true' ? undefined : 1;
      $scope.allPriceList = {};

      var getCustomers = function () {
        if ($scope.priceListData !== '' && $scope.priceListData !== undefined && $scope.priceListData.length > 0){
          $scope.allPriceList.priceList = $scope.priceListData;
        } else {
          server.getAll('priceLists').success(function (data) {
            $scope.allPriceList.priceList = data;
            transferData.data.priceLists = data;
          });
        }
      };

      $scope.configSelectedPriceList = {
        create: false,
        valueField: '_id',
        labelField: 'names',
        render: {
          item: function (item) {
            return '<div>' + item.name + '</div>';
          },
          option: function (item) {
            return '<div>' + item.name + '<small> (' + item.factor + ')</small>' + '</div>';
          }
        },
        searchField: [
          'name',
        ],
        placeholder: 'Seleccione una o mas lista de precios',
        maxItems: maxItems
      };

      $timeout(function () {
        getCustomers();
      }, 250);



      }]
    };
  });
