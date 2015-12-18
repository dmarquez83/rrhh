<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersCollection extends Migration {

	public function up()
	{
    Schema::create('Users', function($collection)
    {
      $collection->unique('username');
    });
	}

  public function down()
  {
    Schema::drop('Users');
  }
}
