<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusCollection extends Migration {

  public function up()
  {
    Schema::create('Bonus', function($collection)
    {
      $collection->unique('code');
      $collection->unique('name');
    });
  }

  public function down()
  {
    Schema::drop('Bonus');
  }
}