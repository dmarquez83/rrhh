<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesCollection extends Migration {

	public function up()
	{
    Schema::create('Roles', function($collection)
    {
      $collection->unique('name');
    });
	}

	public function down()
	{
    Schema::drop('Roles');
	}

}
