<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsCollection extends Migration {

  public function up()
  {
    Schema::create('DocumentConfiguration', function($collection)
    {
      $collection->unique('code');
    });
  }

  public function down()
  {
    Schema::drop('DocumentConfiguration');
  }

}
