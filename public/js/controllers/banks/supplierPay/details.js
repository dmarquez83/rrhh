'use strict';
angular.module('app').controller('DetailsSupplierPayCtrl', [
  '$scope',
  '$modalInstance',
  '$http',
  'selectedSupplierPay',
  function($scope, $modalInstance, $http, selectedSupplierPay){

  	$scope.supplierPay = angular.copy(selectedSupplierPay);
  	$scope.delayDays = 0;
  	$scope.missingDays = 0;
  	var days = moment($scope.supplierPay.expireDate);
    var today = moment();
    var daysLate = days.diff(today, 'days');
    if (daysLate >= 0){
      $scope.delayDays = daysLate;
    } else {
      $scope.missingDays = daysLate;
    }

  	$scope.close = function() {
  	  $modalInstance.close();
  	};

  	$scope.registerExtensionDate = function() {
  		$http.post('supplierPays/registerExtension', $scope.supplierPay).success(function(data){
  			$scope.serverProcess = false;
	        toastr[data.type](data.msg);
	        if (data.type === 'success') {
	          $modalInstance.close();
	        }
  		});
  	};

  }
]);