'use strict';
angular.module('app').controller('EmployeeProfileCtrl', [
  '$scope',
  'server',
  'transferData',
  '$filter',
  function ($scope, server, transferData, $filter) {
    $scope.departments = [];
    $scope.bonus = [];
    $scope.discounts = [];
    $scope.assignedBonus = {};
    $scope.assignedDiscounts = {};
    var masterOffices = [];
    $scope.newSalary = {
      'salary': '',
      'observation': '',
      'date': moment().format()
    };
    $scope.newDepartment = {
      'department': '',
      'observation': '',
      'date': moment().format()
    };
    $scope.employee = transferData.data.employee;

    $scope.registerNewSalary = function () {
      $scope.employee.salaryHistory.push($scope.newSalary);
      var salary = {
          'grossSalary': $scope.newSalary.salary,
          'salaryHistory': angular.copy($scope.employee.salaryHistory)
        };
      server.update('employee', salary, $scope.employee._id).success(function (data) {
      });
    };

    $scope.registerDepartment = function () {
      $scope.employee.departmentHistory.push($scope.newDepartment);
      var department = {
          'department_id': $scope.newDepartment.department._id,
          'office_id': $scope.newDepartment.office._id,
          'departmentHistory': angular.copy($scope.employee.departmentHistory)
        };
      server.update('employee', department, $scope.employee._id).success(function (data) {
      });
    };

    $scope.registerBonus = function () {
      $scope.employee.bonus = _($scope.employee).has('bonus') ? $scope.employee.bonus : [];
      $scope.assignedBonus.date = moment().format();
      $scope.employee.bonus.push($scope.assignedBonus);
      var bonus = { 'bonus': angular.copy($scope.employee.bonus) };
      server.update('employee', bonus, $scope.employee._id).success(function (data) {
      });
    };

    $scope.validateDiscountValue = function() {
      var discountValue = ($scope.assignedDiscounts.discount.type == "Valor" ?
        $scope.assignedDiscounts.discount.value : (parseFloat($scope.employee.grossSalary) * (parseFloat($scope.assignedDiscounts.discount.value))/100));
        if (discountValue > parseFloat($scope.employee.discountsValue)){
          toastr.error("Este descuento excede el valor maximo configurado para este empleado");
          $scope.assignedDiscounts = {};
        }
    }

    $scope.registerDiscount = function () {
      $scope.employee.discounts = _($scope.employee).has('discounts') ? $scope.employee.discounts : [];
      $scope.assignedDiscounts.date = moment().format();
      $scope.employee.discounts.push($scope.assignedDiscounts);
      var discounts = { 'discounts': angular.copy($scope.employee.discounts) };
//      console.log(discounts);
      server.update('employee', discounts, $scope.employee._id).success(function (data) {
      });
    };

    $scope.deleteDiscount = function(index){
      $scope.employee.discounts.splice(index, 1)
      var discounts = { 'discounts': angular.copy($scope.employee.discounts) };
      server.update('employee', discounts, $scope.employee._id).success(function (data) {
      });
    }

    $scope.deleteBonus = function(index){
      $scope.employee.bonus.splice(index, 1);
      console.log($scope.employee.bonus);
      var bonus = { 'bonus': angular.copy($scope.employee.bonus) };
      server.update('employee', bonus, $scope.employee._id).success(function (data) {
      });
    }

    $scope.validateStatus = function(){
      var result = ($scope.employee.status == 'Despido' || $scope.employee.status == 'Renuncia' ? false:true);
      return result;
    }

    $scope.getOffices = function(){
      $scope.offices = _(masterOffices).where({ 'department_id': $scope.newDepartment.department._id });
    }

    server.getAll('offices').success(function (data) {
      masterOffices = data;
    });

    server.getAll('departments').success(function (data) {
      $scope.departments = data;
    });

    server.getAll('bonus').success(function (data) {
      $scope.bonus = data;
    });



    server.getAll('discounts').success(function (data) {
      var discounts = data;
      _(discounts).each(function(discount){
        if(discount.type == "Valor"){
          discount.maskedValue = $filter('currency')(discount.value, "$ ", 2);
        } else {
          discount.maskedValue = discount.value + ' %';
        }
      });
      $scope.discounts = discounts;
    });

    handlePanelAction();
  }
]);