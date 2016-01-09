<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymenthRolesCollection extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
  public function up()
  {
	Schema::create('PaymenthRoles', function($collection)
	{
	  $collection->unique(['identification']);
	});
  }

  public function down()
  {
	Schema::drop('PaymenthRoles');
  }

}
