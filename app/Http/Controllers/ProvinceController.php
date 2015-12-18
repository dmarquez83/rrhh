<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Province;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Province Model
|--------------------------------------------------------------------------
*/

class ProvinceController extends Controller {


	public function index()
	{
		$provinces = Province::all();
		return $provinces;
	}

}