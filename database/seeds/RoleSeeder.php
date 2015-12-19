<?php

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder {

  public function run()
  {
    DB::collection('Role')->delete();

    Role::create([
      "_id"     => "54e15ee2b144fddc0d123456",
      "name"    => "root",
      "modules" => [
        [
          '_id' => '54dd818db144fd340e000049',
          'name' => 'Recursos Humanos',
          'route' => 'recursosHumanos',
          'cssClass' => 'fa-group',
          'templateUrl' => 'views/common/content.html',
          'state' => 'humanResources',
          'order' => 12,
          'isVisible' => TRUE,
          "isSelected"  => TRUE,
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
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
            ],
            [
              'name' => 'Asignación Masiva de Bonos y Descuentos',
              'route' => 'asignacionMasivaBonosDescuentos',
              'templateUrl' => 'views/humanResources/massiveBonusDiscountLoad/new.html',
              'controller' => 'MassiveBonusDiscountLoad',
              'state' => 'massiveBonusDiscountLoad',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => TRUE
            ],
            [
              'name' => 'Carga Masiva de Atrasos',
              'route' => 'cargaMasivaAtrasos',
              'templateUrl' => 'views/humanResources/massiveArrearsLoad/new.html',
              'controller' => 'MassiveArrearsLoad',
              'state' => 'massiveArrearsLoad',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => TRUE
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
              "isSelected"  => TRUE
            ],
            [
              'name' => 'Resumen de Liquidaciones',
              'route' => 'resumenLiquidaciones',
              'templateUrl' => 'views/humanResources/summaryLiquidations/summary.html',
              'controller' => 'SummaryLiquidations',
              'state' => 'summaryLiquidations',
              'isReport'=> 0,
              'modelName'=> '',
              'isVisible' => TRUE,
              "isSelected"  => TRUE
            ],
            [
              'name' => 'Configuración',
              'route' => 'configuracionRecursosHumanos',
              'templateUrl' => 'views/humanResources/configuration/configuration.html',
              'controller' => 'HumanResourcesConfiguration',
              'state' => 'HumanResourcesConfiguration',
              'isVisible' => TRUE,
              "isSelected"  => TRUE
            ]
          ]
        ]
      ]
    ]);
  }

}
