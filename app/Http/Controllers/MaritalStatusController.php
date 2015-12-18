<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\MaritalStatus;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for MaritalStatus Model
|--------------------------------------------------------------------------
*/

class MaritalStatusController extends Controller {


	public function index()
	{
		$maritalStatus = MaritalStatus::all();
		return $maritalStatus;
	}


}