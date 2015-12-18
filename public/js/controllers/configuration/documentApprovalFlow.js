'use strict';
angular.module('app').controller('DocumentApprovalFlowCtrl', [
  '$scope',
  'server',
  'DTOptionsBuilder',
  'optionsDataTable',
  function ($scope, server, DTOptionsBuilder, optionsDataTable) {

    $scope.serverProcess = false;
    $scope.isUpdate = false;
    $scope.documentApprovalFlow = {};
    $scope.documentApprovalFlow.flow = [{}];
    $scope.orders = [];
    $scope.allRoles = {};
    $scope.allRoles.roles = [];
    var tableInstance = {};

    var getDocuments = function(){
      server.getAll('documentsConfiguration').success(function(data){
        $scope.documents = data;
      });
    };

    var getRoles = function () {
      server.getAll('roles').success(function (data) {
        $scope.allRoles.roles = data;
      });
    };

    $scope.configSelectedRoles = {
      create: false,
      valueField: '_id',
      labelField: 'name',
      render: {
        item: function (item, escape) {
          return '<div>' + item.name + '</div>';
        },
        option: function (item, escape) {
          return '<div>' + '<h6>' + item.name + '</h6>' + '</div>';
        }
      },
      searchField: ['name'],
      placeholder: 'Seleccione roles',
    };

    var refreshOrder = function() {
      $scope.orders = [];
      var flowLength = $scope.documentApprovalFlow.flow.length;
      var i = 0;
      for (i; i < flowLength; i++) {
        $scope.orders.push(i+1);
      }
    };

    $scope.addFlowElement = function(){
      $scope.documentApprovalFlow.flow.push({});
      refreshOrder();
    };

    $scope.deleteFlowElement = function(index){
      $scope.documentApprovalFlow.flow.splice(index, 1);
      refreshOrder();
    };

    $scope.validateOrderSelected = function(index){
      var seletedOrder = $scope.documentApprovalFlow.flow[index].order;
      var flowWithOutSelectedOrder = angular.copy($scope.documentApprovalFlow.flow);
      flowWithOutSelectedOrder.splice(index, 1);
      _(flowWithOutSelectedOrder).each(function(flowElement){
        if(seletedOrder == flowElement.order){
          toastr.warning('Este order ya esta selecionado');
          $scope.documentApprovalFlow.flow[index].order = '';
        }
      });
    };

    $scope.validateRolesSelected = function(index){
      var selectedRoles = angular.copy($scope.documentApprovalFlow.flow[index].rol_ids) || [];
      if (selectedRoles.length > 0){
        var lastSelectedRol = selectedRoles[selectedRoles.length - 1];
        var flowWithOutSelectedRoles = angular.copy($scope.documentApprovalFlow.flow);
        flowWithOutSelectedRoles.splice(index, 1);
        _(flowWithOutSelectedRoles).each(function(flowElement){
          _(flowElement.rol_ids).each(function(rolId){
            if(lastSelectedRol == rolId) {
              toastr.warning('Este rol ya esta selecionado');
              $scope.documentApprovalFlow.flow[index].rol_ids = _(selectedRoles).without(lastSelectedRol);
            }
          });
        });
      }
    };

    $scope.clean = function(){
      $scope.documentApprovalFlow = {};
      $scope.documentApprovalFlow.flow = [{}];
      $scope.orders = [];
      $scope.serverProcess = false;
      $scope.isUpdate = true;
      tableInstance.reloadData();
      $scope.documentApprovalFlowForm.$setPristine();
    };

    var save = function(){
      $scope.serverProcess = true;
      server.save('documentsApprovalFlows', $scope.documentApprovalFlow).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    };

    var update = function(){
      $scope.serverProcess = true;
      server.update('documentsApprovalFlows', $scope.documentApprovalFlow, $scope.documentApprovalFlow._id).success(function (data) {
        $scope.serverProcess = false;
        toastr[data.type](data.msg);
        if (data.type == 'success') {
          $scope.clean();
        }
      });
    };

    $scope.save = function(formIsValid){
      if(formIsValid) {
        if($scope.isUpdate) {
          update();
        } else {
          save();
        }
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    };

    $scope.tableColumns = optionsDataTable.generateTableColumns([
      {'field': 'documentCode', 'value': 'C&oacuteldigo de  Documento'},
      {'field': 'flow.length', 'value': 'Cantidad de Aprobaciones'}
    ]);

    $scope.dtOptions = DTOptionsBuilder.newOptions()
      .withOption('ajax', {
        url: 'documentsApprovalFlowsForTable',
        type: 'POST',
        headers: {'X-CSRF-Token': CSRF_TOKEN}
      })
      .withDataProp('data')
      .withOption('serverSide', true)
      .withOption('rowCallback', function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        $('td', nRow).unbind('click');
        $('td', nRow).bind('dblclick', function () {
          $scope.$apply(function () {
            $scope.documentApprovalFlow = angular.copy(aData);
            $scope.isUpdate = true;
            refreshOrder();
          });
        });
        return nRow;
      })

   $scope.getTableInstance = function(dtInstance){
     tableInstance = dtInstance;
   }

    getRoles();
    getDocuments();
    refreshOrder();

  }
]);
