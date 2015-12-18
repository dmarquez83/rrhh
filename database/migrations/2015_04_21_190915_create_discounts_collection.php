<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsCollection extends Migration {

  public function up()
  {
    Schema::create('Discounts', function($collection)
    {
      $collection->unique('code');
      $collection->unique('name');
    });
  }

  public function down()
  {
    Schema::drop('Discounts');
  }
}