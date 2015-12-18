'use strict';
angular.module('app').controller('NewEmployeCtrl', [
  '$scope',
  'documentValidate',
  'server',
  function ($scope, documentValidate, server) {
    $scope.serverProcess = false;
    $scope.employee = {};
    $scope.employee.identification = '';
    $scope.employee.isPassport = false;
    $scope.employee.isDependencyRelationship = true;
    $scope.employee.isDriver = false;
    $scope.employee.discount = false;
    $scope.employee.discountValue = 0;
    $scope.employee.bonusValue = 0;
    $scope.employee.sonNumber = 0;
    $scope.employee.grossSalaryber = 0;
    $scope.employee.responsibilities = 0;
    $scope.employee.bonus = false;
    $scope.employee.conadis = '';
    $scope.employee.telephones = [''];
    $scope.employee.cellphones = [''];
    $scope.employee.emails = [''];
    $scope.departments = {};
    $scope.seccions = {};
    $scope.offices = {};
    $scope.today = moment().format();
    $scope.boolOptions = [{value: true, name: 'Si'}, {value: false, name: 'No'}];
    
    var masterOffices = [];

    var validateTelephones = function(){
      if ($scope.employee.telephones.length == 0){
        toastr.warning('Ingrese al menos un tel&eacute;fono');
        return false;
      }
      return true;
    };

    var validateCellphones = function(){
      if ($scope.employee.cellphones.length == 0){
        toastr.warning('Ingrese al menos un tel&eacute;fono celular');
        return false;
      }
      return true;
    };

    var validateEmails = function(){
      if ($scope.employee.emails.length == 0){
        toastr.warning('Ingrese al menos un correo electr&oacute;nico');
        return false;
      }
      return true;
    };

    var validate = function(){
      if (validateTelephones() && validateEmails() && validateCellphones()){
        return true;
      }
      return false;
    };

    $scope.save = function (formIsValid) {
      if (validate() && formIsValid) {
        $scope.serverProcess = true;
        server.save('employee', $scope.employee).success(function (data) {
          $scope.serverProcess = false;
          toastr[data.type](data.msg);
          if (data.type == 'success') {
            $scope.clean();
          }
        });
      } else {
        toastr.warning("Revisar errores en el formulario");
      }
    };

    $scope.validateIdentification = function () {
      if ($scope.employee.identification != '' && $scope.employee.identification != undefined) {
        var isValidate = documentValidate.validateDocument($scope.employee.identification);
        if (!isValidate) {
          $scope.employee.identification = '';
          $('#identification').focus();
        }
      } else {
        $scope.employee.identification = null;
      }
    };

    $scope.datepickerOptions = {
      format: 'yyyy-mm-dd',
      language: 'es',
      autoclose: true
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

    $scope.cleanBonus = function() {
      $scope.employee.bonusValue = '';
    }

    $scope.cleanDiscounts = function() {
      $scope.employee.discountsValue = '';
    }

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });

    server.getAll('bank').success(function (data) {
      $scope.banks = data;
    });

    server.getAll('offices').success(function (data) {
      masterOffices = data;
    });

    server.getAll('maritalStatus').success(function (data) {
      $scope.maritalsStatus = data;
    });

    $scope.loadSeccion = function () {
      server.getAll('seccions').success(function (data) {
        $scope.seccions = data;
      });
    };

    $scope.getOffices = function () {
      $scope.offices = _(masterOffices).where({ 'department_id': $scope.employee.department_id });
    };

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

    $scope.clean = function () {
      $scope.serverProcess = false;
      $scope.isUpdate = false;
      $scope.employee = {};
      $scope.employee.identification = '';
      $scope.employee.isDriver = false;
      $scope.employee.isPassport = false;
      $scope.employee.discount = false;
      $scope.employee.bonus = false;
      $scope.employee.discountValue = 0;
      $scope.employee.bonusValue = 0;
      $scope.employee.sonNumber = 0;
      $scope.employee.grossSalaryber = 0;
      $scope.employee.responsibilities = 0;
      $scope.employee.disability = false;
      $scope.employee.conadis = '';
      $scope.employee.telephones = [''];
      $scope.employee.cellphones = [''];
      $scope.employee.emails = [''];
      $scope.employeeForm.$setPristine();
    };

    handlePanelAction();
  }
]);
