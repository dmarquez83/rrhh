<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleConfigurationCollection extends Migration {

	public function up()
	{
		Schema::create('ScheduleConfiguration', function($collection)
		{
			$collection->unique('countBell');
			$collection->unique('hourBell');
            $collection->unique('typeBell');
		});
	}


	public function down()
	{
        Schema::drop('ScheduleConfiguration');
	}

}
