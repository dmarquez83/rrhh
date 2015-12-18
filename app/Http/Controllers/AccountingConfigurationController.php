<?php namespace App\Http\Controllers;

use App\Helpers\ResultMsgMaker;
use App\Models\AccountingConfiguration;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for AccountingConfiguration Model
|--------------------------------------------------------------------------
*/

class AccountingConfigurationController extends Controller {

	public function index() 
	{

		$accountingConfigurations = AccountingConfiguration::with('documentConfiguration')->get();
		return $accountingConfigurations;
	}

	public function forTable()
	{
		$params = Input::all();
		$draw = $params['draw'];
		$start = $params['start'];
		$length = $params['length'];

		$columOrderIndex = $params['order'][0]['column'];
		$columOrderDir = $params['order'][0]['dir'];
		$columOrderName = $params['columns'][$columOrderIndex]['data'];

		$searchValue = $params['search']['value'];

		$totalRecords = AccountingConfiguration::count();
		$recordsFiltered = $totalRecords;
		$paymentConditions = AccountingConfiguration::with('documentConfiguration', 'total', 'subtotal', 'iva', 'discount')
		  ->skip($start)
		  ->take($length)
		  ->orderBy($columOrderName, $columOrderDir)
		  ->get();


		if($searchValue!=''){
		  $paymentConditions = $paymentConditions->filter(function($paymentCondition) use($searchValue){
		    if (stripos($paymentCondition, $searchValue)) {return true;};
		    return false;
		  })->values();
		  $recordsFiltered = $paymentConditions->count();
		}

		$returnData = array(
		  'draw' => $draw,
		  'recordsTotal' => $totalRecords,
		  'recordsFiltered' => $recordsFiltered,
		  'data' => $paymentConditions);
		return $returnData;
	}

	public function store() 
	{
		$accountingConfiguration = Input::all();
		if (AccountingConfiguration::create($accountingConfiguration)) {
			return ResultMsgMaker::saveSuccess();
		} else {
			return ResultMsgMaker::error();
		}
	}

	public function update($id)
	{
		$accountingConfiguration = AccountingConfiguration::find($id);
		$newData = Input::all();
		if($accountingConfiguration->update($newData)){
		   return ResultMsgMaker::saveSuccess();
		} else {
		  return ResultMsgMaker::error();
		}
	}

	

}
