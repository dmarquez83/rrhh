<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversCollection extends Migration {

  public function up()
  {
    Schema::create('Drivers', function($collection)
    {
      $collection->unique(['identification']);
    });
  }

  public function down()
  {
    Schema::drop('Drivers');
  }

}
