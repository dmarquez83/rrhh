<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesCollection extends Migration {

  public function up()
  {
    Schema::create('Companies', function($collection)
    {
      $collection->unique('code');
      $collection->unique('name');
      $collection->unique('databaseName');
    });
  }

  public function down()
  {
    Schema::drop('Companies');
  }

}
