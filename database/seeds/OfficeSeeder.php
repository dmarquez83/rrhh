<?php

use App\Models\Office;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder {

  public function run() {
    DB::table('Office')->delete();
  }
}