<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStateCollection extends Migration {
  public function up()
  {
    Schema::create('State', function($collection)
    {
      $collection->unique('code');
      $collection->unique('name');
    });
  }

  public function down()
  {
    Schema::drop('State');
  }
}