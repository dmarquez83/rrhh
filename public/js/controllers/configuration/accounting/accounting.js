'use strict';
angular.module('app').controller('AccountingConfigurationCtrl', [
  '$scope',
  '$rootScope',
  'documentValidate',
  'server',
  '$http',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, $rootScope, documentValidate, server, $http, optionsDataTable, DTOptionsBuilder) {

    var fileName = "configuracion_contabilidad";
    var title = "Configuración contabilidad";

    $scope.accountingConfiguration = {};
    $scope.serverProcess = false;
    $scope.isUpdate = false;
    var tableInstance;

    $scope.paymentWays = $rootScope.PAYMENTWAYS;
    $scope.groupPaymentMethods = _($rootScope.PAYMENTMETHODS).groupBy('paymentWay_id');


    server.getAll('statement').success(function (data) {
      $scope.ledgerAccounts = data;
    });

    $scope.validateDocument = function() {
      if ($scope.accountingConfiguration.documentCode === '015' || $scope.accountingConfiguration.documentCode === '004') {
        var cashPaymentWay = _($rootScope.PAYMENTWAYS).findWhere({name: 'Contado'});
        var paymentMethods = _($rootScope.PAYMENTMETHODS).where({'paymentWay_id': cashPaymentWay._id});
        $scope.paymentWays = [cashPaymentWay];
        $scope.groupPaymentMethods = _(paymentMethods).groupBy('paymentWay_id');
      } else {
        $scope.paymentWays = $rootScope.PAYMENTWAYS;
        $scope.groupPaymentMethods = _($rootScope.PAYMENTMETHODS).groupBy('paymentWay_id');
      }
    };

    var getDocuments = function(){
      $http.post('documentsConfiguration/contable').success(function(data){
        $scope.documents = data;
      });
    };

    var save = function () {
      $scope.serverProcess = true;
      server.save('accountingConfiguration', $scope.accountingConfiguration).success(function (data) {
        $scope.serverProcess = false;
        $scope.accountingConfiguration = {};
        $scope.serverProcess = false;
        $scope.isUpdate = false;
        toastr[data.type](data.msg);
        tableInstance.reloadData();
        $scope.accountingConfigurationForm.$setPristine();
      });
    };

    var update = function () {
      $scope.serverProcess = true;
      server.update('accountingConfiguration', $scope.accountingConfiguration, $scope.accountingConfiguration._id).success(function (data) {
        $scope.serverProcess = false;
        $scope.accountingConfiguration = {};
        $scope.serverProcess = false;
        $scope.isUpdate = false;
        toastr[data.type](data.msg);
        tableInstance.reloadData();
        $scope.accountingConfigurationForm.$setPristine();
      });
    };

    $scope.save = function (formIsValid) {
      if (formIsValid) {
        if ($scope.isUpdate) {
          update();
        } else {
          save();
        }
      } else {
        toastr.warning('Revisar errores en el formulario');
      }
    };

    $scope.clean = function() {
      $scope.accountingConfiguration = {};
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.accountingConfigurationForm.$setPristine();
    };

    var editAccountingConfiguration = function(selectedeAccountingConfiguration){
      $scope.isUpdate = true;
      $scope.accountingConfiguration = angular.copy(selectedeAccountingConfiguration);
      $scope.$digest();
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.tableColumns = optionsDataTable.generateTableColumns([
      {'field': 'documentCode', 'value': 'Codigo'},
      {'field': 'document_configuration.name', 'value': 'Nombre'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('accountingConfiguration/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editAccountingConfiguration))
      .withOption('order', [0, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));

    getDocuments();
    handlePanelAction();

  }
]);