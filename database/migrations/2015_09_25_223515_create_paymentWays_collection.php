<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentWaysCollection extends Migration {

  public function up()
  {
    Schema::create('PaymentWays', function($collection)
    {
      $collection->unique(['code']);
      $collection->unique(['name']);
    });
  }

  public function down()
  {
    Schema::drop('PaymentWays');
  }

}
