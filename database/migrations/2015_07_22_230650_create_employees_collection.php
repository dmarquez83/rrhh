<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesCollection extends Migration {

  public function up()
  {
    Schema::create('Employees', function($collection)
    {
      $collection->unique('identification');
      $collection->unique('code');
    });
  }


  public function down()
  {
    Schema::drop('Employees');
  }

}
