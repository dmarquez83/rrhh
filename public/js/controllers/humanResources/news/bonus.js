'use strict';
angular.module('app').controller('BonusCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  'optionsDataTable',
  'DTOptionsBuilder',
  function ($scope, server, SweetAlert, optionsDataTable, DTOptionsBuilder) {
    $scope.bond = {};
    $scope.bond.type = 'Valor';
    $scope.serverProcess = false;
    $scope.isUpdate = false;
    var title = 'Resumen Bonos';
    var fileName = 'resumen_bonos';
    var tableInstance = {};

    var getBonus = function () {
      server.getAll('bonus').success(function (data) {
        $scope.bonus = data;
      });
    };

    var editBond = function (selectedBond) {
      $scope.bond = selectedBond;
      $scope.isUpdate = true;
      $scope.$digest();
    };

    $scope.clean = function () {
      $scope.isUpdate = false;
      $scope.bond = {};
      $scope.bond.type = 'Valor';
      tableInstance.reloadData();
      getBonus();
      $scope.bonusForm.$setPristine();
    };

    $scope.validateTypeBond = function () {
      if ($scope.bond.type == 'Porcentaje') {
        if ($scope.bond.value > 100) {
          $scope.bond.value = 100;
        }
      }
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('bonus', $scope.bond, $scope.bond._id).success(function (result) {
        $scope.serverProcess = false;
        toastr[result.type](result.msg);
        if(result.type == 'success'){
          $scope.clean();
        }
      });
    }

    var save = function(){
      $scope.serverProcess = true;
      server.save('bonus', $scope.bond).success(function (result) {
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

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este bono?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('bonus', $scope.bond._id).success(function(result){
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

    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'code', title: 'Código'},
      {field: 'name', title: 'Nombre'},
      {field: 'type', title: 'Tipo'},
      {field: 'value', title: 'Valor', class: 'text-right', filter: 'number'}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('bonus/forTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editBond))
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


    getBonus();
    handlePanelAction();
  }
]);