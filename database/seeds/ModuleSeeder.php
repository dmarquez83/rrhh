<?php

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder {

    public function run() {
        DB::collection('Module')->delete();

        Module::create([
          '_id' => '54dd818db144fd340e000049',
          'name' => 'Recursos Humanos',
          'route' => 'recursosHumanos',
          'cssClass' => 'fa-group',
          'templateUrl' => 'views/common/content.html',
          'state' => 'humanResources',
          'order' => 12,
          'isVisible' => TRUE,
          "isSelected"  => FALSE,
          'submodules' => [
            [
              'name' => 'Nuevo Empleado',
              'route' => 'nuevoEmpleado',
              'templateUrl' => 'views/humanResources/employees/newEmployee.html',
              'controller' => 'NewEmploye',
              'state' => 'newEmploye',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Resumen Empleado',
              'route' => 'resumenEmpleados',
              'templateUrl' => 'views/humanResources/employees/summaryEmployees.html',
              'controller' => 'SummaryEmployees',
              'state' => 'summaryEmploye',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Reporte de Empleados',
              'route' => 'reporteEmpleados',
              'templateUrl' => 'views/humanResources/employees/employeesReport.html',
              'controller' => 'EmployeesReport',
              'state' => 'employeesReport',
              'isReport'=> 1,
              'modelName'=> 'Employee',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Perfil Empleado',
              'route' => 'perfilEmpleado',
              'templateUrl' => 'views/humanResources/employees/employeeProfile.html',
              'controller' => 'EmployeeProfile',
              'state' => 'employeeProfile',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => FALSE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Departamentos',
              'route' => 'departamentos',
              'templateUrl' => 'views/humanResources/departments/departments.html',
              'controller' => 'Departments',
              'state' => 'departments',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Cargos',
              'route' => 'cargos',
              'templateUrl' => 'views/humanResources/departments/office.html',
              'controller' => 'Office',
              'state' => 'office',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Bonos',
              'route' => 'crearBonos',
              'templateUrl' => 'views/humanResources/news/bonus.html',
              'controller' => 'Bonus',
              'state' => 'bonus',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Descuentos',
              'route' => 'descuentos',
              'templateUrl' => 'views/humanResources/news/discounts.html',
              'controller' => 'Discounts',
              'state' => 'discounts',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Liquidación del Rol',
              'route' => 'liquidacionRol',
              'templateUrl' => 'views/humanResources/rolLiquidation/new.html',
              'controller' => 'RolLiquidation',
              'state' => 'rolLiquidation',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Impresión del Rol',
              'route' => 'impresionRol',
              'templateUrl' => 'views/humanResources/rolPrinting/new.html',
              'controller' => 'RolPrinting',
              'state' => 'rolPrinting',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ],
            [
              'name' => 'Configuración',
              'route' => 'configuracionRecursosHumanos',
              'templateUrl' => 'views/humanResources/configuration/configuration.html',
              'controller' => 'HumanResourcesConfiguration',
              'state' => 'HumanResourcesConfiguration',
              'isVisible' => TRUE,
              "isSelected"  => FALSE
            ]
          ]
        ]);

    }

}
