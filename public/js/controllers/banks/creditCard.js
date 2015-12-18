'use strict';
angular.module('app').controller('CreditCardCtrl', [
  '$scope',
  '$filter',
  'documentValidate',
  'server',
  'transferData',
  'SweetAlert',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope,  $filter, documentValidate, server, transferData, SweetAlert, optionsDataTable,DTOptionsBuilder) {

    $scope.serverProcess = false;
    $scope.isUpdate = false;
    $scope.creditCard = {};
    $scope.creditCards = [];
    $scope.panelExpand = '';
    $scope.employees = [''];
    var fileName = 'resumen_tarjetas_crédito_' + moment().format('YYYYMMDD');
    var title = 'Resumen Tarjetas de Crédito';
    var tableInstance = {};


    var update = function(){
      $scope.serverProcess = true;
      server.update('creditCards', $scope.creditCard, $scope.creditCard._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.clean();
        }
      });
    };

    var save = function(){
      $scope.serverProcess = true;
      server.save('creditCards', $scope.creditCard).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type === 'success') {
          $scope.clean();
        }
      });
    };

    $scope.save = function (formIsValid) {
      if(formIsValid){
        if ($scope.isUpdate) {
          update();
        } else {
          save();
        }
      } else{
        toastr.warning('Revisar errores en el formulario');
      }
    };

    var editCreditCard = function(selectedCreditCard){
      $scope.isUpdate = true;
      $scope.creditCard = angular.copy(selectedCreditCard);
      $scope.$digest();
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {'field': 'name', 'title': 'Nombre Tarjeta'},
      {'field': 'number', 'title': 'Número'},
      {'field': 'employee', 'title': 'Empleado Asignado', 'render': function (data) {
        var completeName = "";
        if(data != null){
          completeName = data.names + ' ' + data.surnames;
        }
        return completeName;
      }},
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('creditCards/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editCreditCard))
      .withOption('order', [0, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));


    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };

    $scope.delete = function () {
      SweetAlert.swal({
          title: 'Está seguro de eliminar esta tarjeta de crédito?',
          text: 'Si elimina este registro no lo podrá recuperar',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#DD6B55',confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No, cancelar',
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            $scope.serverProcess = true;
            server.delete('creditCards',  $scope.creditCard._id).success(function(result){
              $scope.serverProcess = false;
              if(result.type === 'success') {
                $scope.serverProcess = false;
                SweetAlert.swal('Eliminado!', result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal('Error!', result.msg, result.type);
              }
            });
          }
        });
    };

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.creditCard = {};
      $scope.employees = [''];
      $scope.creditCardForm.$setPristine();
      reloadData();
    };

    server.getAll('statement').success(function (data) {
      $scope.assets = _(data).filter(function (ledgerAccounts) {
          return true;
        ;
      });
    });

    $scope.maximize = function () {
      if ($scope.panelExpand === 'panel-expand') {
        $scope.panelExpand = '';
        $scope.dtOptions.scrollY = optionsDataTable.scrollY65;
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
    };

    handlePanelAction();
  }
]);
