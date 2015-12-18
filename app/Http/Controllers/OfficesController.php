<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Office;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Office Model
|--------------------------------------------------------------------------
*/

class OfficesController extends Controller {

	public function index()
	{
		$offices = Office::with('department')->get();
		return $offices;
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

    $totalRecords = Office::count();
    $recordsFiltered = $totalRecords;
    $offices = Office::skip($start)
      ->with('department')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $offices = $offices->map(function($office){
      $newOffice = $office;

      return $newOffice;
    });

    if($searchValue!=''){
      $offices = $offices->filter(function($office) use($searchValue){
        if (stripos($office, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $offices->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $offices);
    return $returnData;
  }

	public function store()
	{
    $office  = Input::all();
    $officeExists = Office::create($office);
    if($officeExists){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function update($id)
	{
    $office = Input::all();
    $savedOffice = Office::find($id);
    if($savedOffice->update($office)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function destroy($id)
	{
    $canRemove = DocumentReferenceVerificator::verify("office_id", $id, ['Employee']);
    if($canRemove === true){
      if (Office::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'cargo', '', $modelName);
    }
	}
}
