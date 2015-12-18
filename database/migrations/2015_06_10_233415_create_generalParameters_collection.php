<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralParametersCollection extends Migration {

  public function up()
  {
    Schema::create('GeneralParameters', function($collection)
    {
      $collection->unique('code');
    });
  }

  public function down()
  {
    Schema::drop('GeneralParameters');
  }
}
