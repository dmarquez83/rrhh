<?php

use App\Models\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountsSeeder extends Seeder {

  public function run() {
    DB::table('Discounts')->delete();
  }
}