<?php namespace App\Http\Controllers;

use App\Models\PaymenthRoles;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

class PaymenthRolesController extends Controller {


  public function index()
  {
	$PaymenthRoles = PaymenthRoles::all();
	return $PaymenthRoles;
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

	$totalRecords = PaymenthRoles::count();
	$recordsFiltered = $totalRecords;
	$PaymenthRoles = PaymenthRoles::skip($start)
	  ->take($length)
	  ->orderBy($columOrderName, $columOrderDir)
	  ->get();


	if($searchValue!=''){
	  $PaymenthRoles = $PaymenthRoles->filter(function($paymentRoles) use($searchValue){
		if (stripos($paymentRoles, $searchValue)) {return true;};
		return false;
	  })->values();
	  $recordsFiltered = $PaymenthRoles->count();
	}

	$returnData = array(
	  'draw' => $draw,
	  'recordsTotal' => $totalRecords,
	  'recordsFiltered' => $recordsFiltered,
	  'data' => $PaymenthRoles);
	return $returnData;
  }

  public function store()
  {
	$paymentRoles = Input::all();
	var_dump($paymentRoles);
	if(PaymenthRoles::create($paymentRoles)){
	  return ResultMsgMaker::saveSuccess();
	}else{
	  return ResultMsgMaker::error();
	}
  }

  public function update($id)
  {
	$condition = Input::all();
	$savedcondition = PaymenthRoles::find($id);
	if($savedcondition->update($condition)){
	  return ResultMsgMaker::updateSuccess();
	}else{
	  return ResultMsgMaker::error();
	}
  }

  public function destroy($id)
  {
	//$modelList = ['PaymenthRoles'];
	//$canRemove = DocumentReferenceVerificator::verify(['paymentMethod_id'], $id, $modelList);
	$canRemove = true;
	if($canRemove === true){
	  if (PaymenthRoles::find($id)->delete()) {
		return ResultMsgMaker::deleteSuccess();
	  } else {
		return ResultMsgMaker::error();
	  }
	} else {
	  $modelName = $canRemove['modelName'];
	  $modelName = Lang::get('modelNames.'.$modelName);

	  return ResultMsgMaker::errorCannotDelete('el', 'método de pago', '', $modelName);
	}
  }

}
