<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransportsCollection extends Migration {

  public function up()
  {
    Schema::create('Transports', function($collection)
    {
      $collection->unique(['plate']);
    });
  }

  public function down()
  {
    Schema::drop('Transports');
  }
}

