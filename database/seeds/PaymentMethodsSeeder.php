<?php

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder{

  public function run(){
    DB::collection('PaymentMethods')->delete();

    PaymentMethod::create(array(
      'code' => '00001',
      'name' => 'Efectivo',
      'paymentWay_id' => '22afa93277e5da12090001c0',
    ));

    PaymentMethod::create(array(
      'code' => '00002',
      'name' => 'Cheque',
      'paymentWay_id' => '22afa93277e5da12090001c0',
    ));

    PaymentMethod::create(array(
      'code' => '00003',
      'name' => 'Transferencia Bancaria',
      'paymentWay_id' => '22afa93277e5da12090001c0',
    ));

    PaymentMethod::create(array(
      'code' => '00004',
      'name' => 'Tarjeta de Crédito',
      'paymentWay_id' => '22afa93277e5da12090001c0',
    ));

    PaymentMethod::create(array(
      'code' => '00005',
      'name' => 'Crédito Directo',
      'paymentWay_id' => '33afa93277e5da12090001c0',
    ));

    PaymentMethod::create(array(
      'code' => '00006',
      'name' => 'Pagaré',
      'paymentWay_id' => '33afa93277e5da12090001c0',
    ));
  }

}

