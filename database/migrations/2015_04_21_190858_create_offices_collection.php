<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficesCollection extends Migration {

  public function up()
  {
    Schema::create('Offices', function($collection)
    {
      $collection->unique(['code','name','department_id']);
    });
  }

  public function down()
  {
    Schema::drop('Offices');
  }
}