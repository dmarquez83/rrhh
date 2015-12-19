<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Bonus;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;


class BonusController extends Controller {

  public function index()
  {
    $bonus = Bonus::all();
    return $bonus;
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

    $totalRecords = Bonus::count();
    $recordsFiltered = $totalRecords;
    $bonus = Bonus::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $bonus = $bonus->map(function($department){
      $newDepartment = $department;

      return $newDepartment;
    });

    if($searchValue!=''){
      $bonus = $bonus->filter(function($department) use($searchValue){
        if (stripos($department, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $bonus->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $bonus);
    
    return $returnData;
  }

  public function store()
  {
    $bond = Input::all();
    $bondCreated = Bonus::create($bond);
    if($bondCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $bond = Input::all();
    $savedBond = Bonus::find($id);
    if($savedBond->update($bond)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify("bonus_id", $id, ['Employee']);
    if($canRemove === true){
      if ($bonus = Bonus::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'bono', '', $modelName);
    }
  }
}
