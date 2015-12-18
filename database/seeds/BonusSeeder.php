<?php

use App\Models\Bonus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonusSeeder extends Seeder {

  public function run() {
    DB::table('Bonus')->delete();
  }
}