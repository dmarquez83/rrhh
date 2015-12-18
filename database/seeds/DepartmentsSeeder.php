<?php

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder {

  public function run() {
    DB::table('Departments')->delete();
  }
}