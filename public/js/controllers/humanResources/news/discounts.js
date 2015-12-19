'use strict';
angular.module('app').controller('DiscountsCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, server, SweetAlert, optionsDataTable, DTOptionsBuilder) {

    $scope.discount = {};
    $scope.discount.type = 'Valor';
    $scope.serverProcess = false;
    $scope.isUpdate = false;
    var title = 'Resumen Descuentos';
    var fileName = 'resumen_descuentos';
    var tableInstance = {};

    var getDiscounts = function () {
      server.getAll('discounts').success(function (data) {
        $scope.discounts = data;
      });
    };

    var updateDiscount = function (selectedDiscount) {
      $scope.discount = selectedDiscount;
      $scope.isUpdate = true;
      $scope.$digest();
    };

    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.discount = {};
      $scope.discount.type = 'Valor';
      tableInstance.reloadData();
      getDiscounts();
      $scope.discountsForm.$setPristine();
    };

    $scope.validateTypeDiscount = function () {
      if ($scope.discount.type == 'Porcentaje') {
        if ($scope.discount.value > 100) {
          $scope.discount.value = 100;
        }
      }
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('discounts', $scope.discount, $scope.discount._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('discounts', $scope.discount).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    $scope.save = function (formIsValid) {
      if(formIsValid){
        if ($scope.isUpdate) {
          update();
        } else {
          save();
        }
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    };

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'code', title: 'Código'},
      {field: 'name', title: 'Nombre'},
      {field: 'type', title: 'Tipo'},
      {field: 'value', title: 'Valor', class: 'text-right', filter: 'number'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('discounts/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(updateDiscount))
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY65)
      .withOption('dom', optionsDataTable.dom)
      .withOption('bProcessing', true)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'))
      .withOption("stateSave", true)
      .withOption('stateSaveCallback', optionsDataTable.saveState(title))
      .withOption('stateLoadCallback', optionsDataTable.loadState(title));

    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };  

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este descuento?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('discounts', $scope.discount._id).success(function(result){
              if(result.type == 'success') {
                $scope.serverProcess = false;
                SweetAlert.swal("Eliminado!", result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
                $scope.clean();
              }
            })
          }
        });
    };

    getDiscounts();
    handlePanelAction();
  }
]);