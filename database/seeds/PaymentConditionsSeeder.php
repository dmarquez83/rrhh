<?php

use App\Models\PaymentCondition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentConditionsSeeder extends Seeder{

  public function run(){
    DB::collection('PaymentConditions')->delete();

    PaymentCondition::create(array(
      'code' => 'cond001',
      'name' => 'Condición 1',
      'days' => [30]
    ));

    PaymentCondition::create(array(
      'code' => 'cond002',
      'name' => 'Condición 2',
      'days' => [30,60,90]
    ));

    PaymentCondition::create(array(
      'code' => 'cond003',
      'name' => 'Condición 3',
      'days' => [30,60,90,120,150,180]
    ));

    PaymentCondition::create(array(
      'code' => 'cond004',
      'name' => 'Condición 4',
      'days' => [30,60,90,120,150,180,210,240,270,300,330,360]
    ));

    PaymentCondition::create(array(
      'code' => 'cond005',
      'name' => 'Condición 5',
      'days' => [30,60,90,120,150,180,210,240,270,300,330,360,390,420,450,480,510,540,570,600,630,660,690,720]
    ));


  }

}

