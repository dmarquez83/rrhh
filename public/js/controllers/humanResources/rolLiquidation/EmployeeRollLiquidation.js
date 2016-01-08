'use strict';
angular.module('app').controller('EmployeeRollLiquidation', [
    '$scope',
    '$modalInstance',
    'server',
    '$rootScope',
    function ($scope, $modalInstance, server, $rootScope) {
        $scope.rolLiquidation = {};
        $scope.rolLiquidation.departmentName = '';
        $scope.departments = [];
        $scope.employees = [];
        $scope.employeSelections = [];
        $scope.countEmployee = 0;

        $scope.$on('employees', function (event, values) {
            $scope.employeSelections = values.employeSelections;
        });

        server.getAll('departments').success(function (data) {
            $scope.departments = data;
        });

         server.post('getEmployees').success(function(result){
            $scope.employees = (result);
            $scope.countEmployee = $scope.employees.length;
        });

        $scope.listEmployees = function(){
            server.post('getEmployees').success(function(result){
                if($scope.rolLiquidation.department_id){
                    $scope.employees = _(result).where({ 'department_id':  $scope.rolLiquidation.department_id });
                }
                else
                {
                    $scope.employees = result;
                }
            });
        };

        $scope.checkAll = function () {
            if ($scope.selectedAll) {
                $scope.selectedAll = true;
                $scope.countEmployee = $scope.employees.length;
            } else {
                $scope.selectedAll = false;
                $scope.countEmployee = 0;
            }
            angular.forEach($scope.employees, function (employe) {
                employe.Selected = $scope.selectedAll;
            });

        };

        $scope.countCheck = function(){
            var cuenta = 0;
            angular.forEach($scope.employees, function (employe) {
                if(employe.Selected){
                    cuenta++;
                }
            });
            $scope.countEmployee = cuenta;
        };

        $scope.saveEmploye = function(){
            var cuenta = 0;
            angular.forEach($scope.employees, function (employe) {
                if(employe.Selected){
                    $scope.employeSelections[cuenta] = employe;
                    cuenta++;
                }
            });
            $rootScope.$broadcast('employees', { employeSelections: $scope.employeSelections });

            $modalInstance.dismiss();
        };




        $scope.cancel = function () {
            $modalInstance.dismiss();
        }


        handlePanelAction();
    }

]);
