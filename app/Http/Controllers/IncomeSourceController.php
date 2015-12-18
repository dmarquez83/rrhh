<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\IncomeSource;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class IncomeSourceController extends Controller {

	public function index()
	{
		$incomeSources = IncomeSource::all();
		return $incomeSources;
	}


}