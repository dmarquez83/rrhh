<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentMethodsCollection extends Migration {

  public function up()
  {
    Schema::create('PaymentMethods', function($collection)
    {
      $collection->unique(['code']);
      $collection->unique(['name']);
    });
  }

  public function down()
  {
    Schema::drop('PaymentMethods');
  }

}
