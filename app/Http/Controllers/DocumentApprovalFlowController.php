<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\DocumentApprovalFlow;
use Illuminate\Support\Facades\Input;
use App\Helpers\ResultMsgMaker;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for DocumentApprovalFlow Model
|--------------------------------------------------------------------------
*/

class DocumentApprovalFlowController extends Controller {

	public function index()
	{
		//
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

		$totalRecords = DocumentApprovalFlow::warehouse()->count();
		$recordsFiltered = $totalRecords;
		$documentsApprovalFlows = DocumentApprovalFlow::warehouse()
		    ->skip($start)
			->take($length)
			->orderBy($columOrderName, $columOrderDir)
			->get();

		if($searchValue!=''){
			$documentsApprovalFlows = $documentsApprovalFlows->filter(function($documentApprovalFlow) use($searchValue){
				if (stripos($documentApprovalFlow, $searchValue)) {return true;};
				return false;
			})->values();
			$recordsFiltered = $documentsApprovalFlows->count();
		}

		$returnData = array(
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $recordsFiltered,
			'data' => $documentsApprovalFlows);
		return $returnData;
	}

	public function getByParameterPost()
  {
    $parameter = Input::all();
    $documentFlow = DocumentApprovalFlow::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $documentFlow;
  }


	public function store()
	{
		$documentApprovalFlow = Input::all();

		if (DocumentApprovalFlow::create($documentApprovalFlow)) {
			return ResultMsgMaker::saveSuccess();
		} else {
			return ResultMsgMaker::error();
		}

	}

	public function update($id)
	{
		$newData = Input::all();
		$savedDocumentApprovalFlow = DocumentApprovalFlow::find($id);

		if($savedDocumentApprovalFlow->update($newData)){
			return ResultMsgMaker::saveSuccess();
		} else {
			return ResultMsgMaker::error();
		}
	}


	public function destroy($id)
	{

	}

}
