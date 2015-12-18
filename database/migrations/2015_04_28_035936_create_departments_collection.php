<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsCollection extends Migration {

  public function up()
  {
    Schema::create('Departments', function($collection)
    {
      $collection->unique('code');
      $collection->unique('name');
    });
  }

  public function down()
  {
    Schema::drop('Departments');
  }
}