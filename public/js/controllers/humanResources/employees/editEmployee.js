'use strict';
angular.module('app').controller('EditEmployeeCtrl', [
  '$scope',
  '$modalInstance',
  '$timeout',
  'server',
  'selectedEmployee',
  'transferData',
  'SweetAlert',
  function ($scope, $modalInstance, $timeout, server, selectedEmployee, transferData, SweetAlert) {
    $scope.isUpdate = true;
    $scope.serverProcess = false;
    $scope.employee = {};
    $scope.today = moment().format();
    $scope.employee = selectedEmployee;
    $scope.maritalsStatus = transferData.data.maritalsStatus;
    $scope.departments = transferData.data.departments;
    $scope.banks = transferData.data.banks;
    $scope.offices = [];
    $scope.boolOptions = [{value: true, name: 'Si'}, {value: false, name: 'No'}];
    var masterOffices = transferData.data.offices;

    $scope.update = function () {
      $scope.serverProcess = true;
      server.update('employee', $scope.employee, $scope.employee._id).success(function (data) {
        $modalInstance.close();
        toastr[data.type](data.msg);
        $scope.employee = {};
        $scope.serverProcess = false;
      });
    };

    $scope.delete = function () {
      $scope.serverProcess = true;
      SweetAlert.swal({
          title: "Está seguro de eliminar este Empleado?",
          text: "Si elimina este registro no lo podrá recuperar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",confirmButtonText: "Si, eliminar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: true,
          closeOnCancel: true },
        function(isConfirm){
          if (isConfirm) {
            server.delete('employee', $scope.employee._id).success(function(result){
              $scope.serverProcess = false;
              if(result.type == 'success') {
                SweetAlert.swal("Eliminado!", result.msg, result.type);
                $modalInstance.close();
              } else {
                SweetAlert.swal("Error!", result.msg, result.type);
              }
            })
          }
        });
    };


    $scope.cleanIdentification = function() {
      $scope.employee.identification = '';
    }

    $scope.cleanDisability = function() {
      $scope.employee.conadis = '';
    }

    $scope.cleanMaritalStatus = function() {
      $scope.employee.spouseName = '';
    }

    $scope.cleanPaymentMethod = function() {
      $scope.employee.bank_id = '';
      $scope.employee.bankAcountNumber = '';
      $scope.employee.bankAccountType = '';
    }

    $scope.isMarried = function(){
      var id = _($scope.employee).has('maritalStatus_id') ? $scope.employee.maritalStatus_id : '';
      if(id != '') {
        var maritalStatusSelected = _($scope.maritalsStatus).findWhere({'_id': id}).name;
        if (maritalStatusSelected == 'Casado' || maritalStatusSelected == 'Unión Libre') {
          return true;
        }
      }
      return false;
    }

    $scope.addTelephone = function () {
      $scope.employee.telephones.push('');
    };
    $scope.addCellphone = function () {
      $scope.employee.cellphones.push('');
    };
    $scope.addEmail = function () {
      $scope.employee.emails.push('');
    };
    $scope.deleteTelephone = function (index) {
      $scope.employee.telephones.splice(index, 1);
    };
    $scope.deleteCellphone = function (index) {
      $scope.employee.cellphones.splice(index, 1);
    };
    $scope.deleteEmail = function (index) {
      $scope.employee.emails.splice(index, 1);
    };

    $scope.getOffices = function () {
      $scope.offices = _(masterOffices).where({ 'department_id': $scope.employee.department_id });
    };

    $scope.validateStatus = function(){
      var result = ($scope.employee.status == 'Despido' || $scope.employee.status == 'Renuncia' ? true:false);
      return result;
    }

    $scope.employee = selectedEmployee;
    $timeout(function () {
      $scope.offices = _(masterOffices).where({ 'department_id': $scope.employee.department_id });
    }, 1000);


  }
]);