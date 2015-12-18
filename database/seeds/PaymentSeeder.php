<?php

use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder{

  public function run(){
    DB::collection('Payment')->delete();

    Payment::create(array(
      'code' => 'E', 
      'name' => 'Efectivo'
    ));

    Payment::create(array(
      'code' => 'C', 
      'name' => 'Cheque'
    ));

    Payment::create(array(
      'code' => 'T', 
      'name' => 'Tarjeta de Crédito'
    ));

    Payment::create(array(
      'code' => 'CD', 
      'name' => 'Crédito Directo'
    ));

  }

}

