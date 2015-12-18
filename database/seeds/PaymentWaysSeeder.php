<?php

use App\Models\PaymentWay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentWaysSeeder extends Seeder{

  public function run(){
    DB::collection('PaymentWays')->delete();

    PaymentWay::create(array(
      '_id' => '22afa93277e5da12090001c0',
      'code' => '00001',
      'name' => 'Contado'
    ));

    PaymentWay::create(array(
      '_id' => '33afa93277e5da12090001c0',
      'code' => '00002',
      'name' => 'Crédito'
    ));
  }

}

