<?php

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesSeeder extends Seeder {

  public function run() {
    DB::table('Employees')->delete();
  }
}