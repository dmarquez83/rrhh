<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehousesCollection extends Migration {

	public function up()
	{
    Schema::create('Warehouses', function($collection)
    {
      $collection->unique(['code', 'company_id']);
      $collection->unique('name');
      $collection->unique('series');
    });
	}

	public function down()
	{
    Schema::drop('Warehouses');
	}

}
