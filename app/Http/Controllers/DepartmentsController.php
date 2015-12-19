<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Department;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Department Model
|--------------------------------------------------------------------------
*/

class DepartmentsController extends Controller {

	public function index()
	{
		$departments = Department::all();
		return $departments;
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

    $totalRecords = Department::count();
    $recordsFiltered = $totalRecords;
    $departments = Department::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $departments = $departments->filter(function($department) use($searchValue){
        if (stripos($department, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $departments->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $departments);
    return $returnData;
  }

	public function store()
	{
    $department = Input::all();
    $departmentCreated = Department::create($department);
    if($departmentCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function update($id)
	{
    $department = Input::all();
    $savedDepartment = Department::find($id);
    if($savedDepartment->update($department)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function destroy($id)
	{
    $canRemove = DocumentReferenceVerificator::verify("department_id", $id, ['Employee','Office']);
    if($canRemove === true){
      if (Department::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'departamento', '', $modelName);
    }
  }
}
