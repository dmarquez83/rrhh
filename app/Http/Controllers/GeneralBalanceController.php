<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\GeneralBalance;
use App\Models\GeneralMajor;
use App\Helpers\ResultMsgMaker;
use App\Helpers\GeneralBalanceMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for GeneralBalance Model
|--------------------------------------------------------------------------
*/

class GeneralBalanceController extends Controller {

	public function index()
	{

	}

	public function forTable()
	{

		\DB::collection('GeneralBalance')->delete();

		$params = Input::all();
		$draw = $params['draw'];
		$start = $params['start'];
		$length = $params['length'];
		$startDate = isset($params['startDate']) ? $params['startDate']: '';
    $endDate = isset($params['endDate']) ? $params['endDate']: '';

    if ($startDate != '' && $endDate != ''){
    	GeneralBalanceMaker::generate([$startDate, $endDate]);
    }
		
		$columOrderIndex = $params['order'][0]['column'];
		$columOrderDir = $params['order'][0]['dir'];
		$columOrderName = $params['columns'][$columOrderIndex]['data'];

		$searchValue = $params['search']['value'];

		$totalRecords = GeneralBalance::count();
		$recordsFiltered = $totalRecords;
		$generalBalance = GeneralBalance::skip($start)
			->take($length)
			->orderBy($columOrderName, $columOrderDir)
			->get();

		if($searchValue!=''){
			$generalBalance = $generalBalance->filter(function($account) use($searchValue){
				if (stripos($account, $searchValue)) {return true;};
				return false;
			})->values();
			$recordsFiltered = $generalBalance->count();
		}

		$returnData = array(
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $recordsFiltered,
			'data' => $generalBalance);
		return $returnData;
	}

}
