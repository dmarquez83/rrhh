<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\HumanResourcesConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\ResultMsgMaker;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for HumanResourcesConfiguration Model
|--------------------------------------------------------------------------
*/

class HumanResourcesConfigurationController extends Controller {

	public function index()
	{
    $configuration = HumanResourcesConfiguration::first();
    return $configuration;
	}


	public function create()
	{

	}


	public function store()
	{
    $configuration = Input::get();
    HumanResourcesConfiguration::truncate();
    if(HumanResourcesConfiguration::create($configuration)){
      return ResultMsgMaker::saveSuccess();
    }
    return ResultMsgMaker::error();
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update($id)
	{
		//
	}

	public function destroy($id)
	{
		//
	}

}
