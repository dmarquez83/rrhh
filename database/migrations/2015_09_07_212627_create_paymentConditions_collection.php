<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentConditionsCollection extends Migration {

  public function up()
  {
    Schema::create('PaymentConditions', function($collection)
    {
      $collection->unique(['code']);
      $collection->unique(['name']);
    });
  }

  public function down()
  {
    Schema::drop('PaymentConditions');
  }

}
