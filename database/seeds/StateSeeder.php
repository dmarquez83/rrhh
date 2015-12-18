<?php

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder{

  public function run(){
    DB::collection('State')->delete();

  }
}