'use strict';
angular.module('app').controller('TariffHeadingCtrl', [
  '$scope',
  'server',
  'SweetAlert',
  'transferData',
  'optionsDataTable',
  'dataTablePDFMaker',
  'dataTableXLSXMaker',
  'DTOptionsBuilder',
  'DTColumnBuilder',
  function ($scope, server, SweetAlert, transferData, optionsDataTable, dataTablePDFMaker, dataTableXLSXMaker, DTOptionsBuilder, DTColumnBuilder) {

    $scope.isUpdate = false;
    $scope.isServerProcess = false;
    $scope.tariffHeading = {};
    $scope.tariffHeadings = [];
    $scope.masterTariffHeadings = [];
    $scope.tariffHeading.isInen = false;
    $scope.tariffHeading.salvaguardia = 0;
    $scope.tariffHeading.advaloren = 0;
    $scope.panelExpand = '';
    var fileName = 'ResumenPartidasArancelarias_' + moment().format('YYYYMMDD');
    var title = 'Resumen Partidas Arancelarias';
    var tableInstance = {};

    var loadTariffHeadings = function () {
      server.getAll('tariffHeadings').success(function (data) {
        $scope.masterTariffHeadings = angular.copy(data);
        $scope.tariffHeadings = angular.copy(data);
      });
    };

    var save = function(){
      server.save('tariffHeadings', $scope.tariffHeading).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var update = function(){
      server.update('tariffHeadings', $scope.tariffHeading, $scope.tariffHeading._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    }

    var filterCategories = function(selectedTariffHeading){
      var filterTariffHeadings = _($scope.masterTariffHeadings).without(selectedTariffHeading);
      $scope.tariffHeadings = angular.copy(filterTariffHeadings);
    }

    $scope.save = function(formIsValid) {
      if(formIsValid) {
        $scope.serverProcess = true;
        if($scope.isUpdate === true){
          update();
        }else {
          save();
        }
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    }

    var editTariffHeading = function(selectedTariffHeading){
      $scope.isUpdate = true;
      var selectedTariffHeadingWithoutParentObject = _(selectedTariffHeading).omit('parent');
      $scope.tariffHeading = angular.copy(selectedTariffHeadingWithoutParentObject);
      filterCategories(selectedTariffHeading);
      $scope.$digest();
    };


    $scope.tableColumns = optionsDataTable.createTableColumns([
      {field: 'type', title: 'Tipo Elemento', render: function (data) {
        var result = (data === 1 ? 'Sección' : data === 2 ? 'Partida' : 'Subpartida');
        return result;
      }},
      {field: 'code', title: 'Código Partida Arancelaria'},
      {field: 'name', title: 'Nombre'},
      {field: 'salvaguardia', title: '% Salvaguardia', class: 'text-right', render: function(data) {
        var percentage = math.multiply(data, 100);
        return math.round(percentage, 2) + " %";
      }},
      {field: 'advaloren', title: '% Advaloren', class: 'text-right', render: function(data) {
        var percentage = math.multiply(data, 100);
        return math.round(percentage, 2) + " %";
      }},
      {field: 'isInen', title: 'Tiene Inen', class: 'text-right', render: function (data) {
        var result = (data === true ? 'Si' : 'No');
        return result;
      }}
    ]);

    $scope.dtOptions = DTOptionsBuilder
      .fromSource(optionsDataTable.fromSource('tariffHeadingForTable'))
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', optionsDataTable.rowCallback(editTariffHeading))
      .withOption('order', [2, 'desc'])
      .withOption('iDisplayLength', 25)
      .withOption('deferRender', true)
      .withOption('scrollY', optionsDataTable.scrollY70)
      .withOption('dom', optionsDataTable.dom)
      .withOption('buttons', optionsDataTable.buttons(fileName, title, 'landscape','A4'));


    var reloadData = function(){
      tableInstance.reloadData();
    };

    $scope.getTableInstance = function(dtInstance){
      tableInstance = dtInstance;
    };


    $scope.clean = function(){
      $scope.isUpdate = false;
      $scope.serverProcess = false;
      $scope.tariffHeading = {};
      $scope.tariffHeading.isInen = false;
      $scope.tariffHeading.salvaguardia = 0;
      $scope.tariffHeading.advaloren = 0;
      $scope.tariffHeadingForm.$setPristine();
      loadTariffHeadings();
      reloadData();
    }


    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar esta Partida Arancelaria?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('tariffHeadings', $scope.tariffHeading._id).success(function(result){
              $scope.serverProcess = false;
              if(result.type == 'success') {
                SweetAlert.swal("Eliminado!", result.msg, result.type);
                $scope.clean();
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
              }
            })
          }
        });
    };

    $scope.maximize = function () {
      if ($scope.panelExpand == 'panel-expand') {
        $scope.panelExpand = '';
        $scope.dtOptions.scrollY = $(window).height() * 0.9 * 0.7;
      } else {
        $scope.panelExpand = 'panel-expand';
        $scope.dtOptions.scrollY = $(window).height() - 200;
      }
    };

    loadTariffHeadings();
    handlePanelAction();
  }
]);

