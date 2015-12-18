<?php namespace App\Http\Controllers;

use App\Models\FixedAssetType;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for FixedAssetType Model
|--------------------------------------------------------------------------
*/


class FixedAssetTypesController extends Controller {


  public function index()
  {
    $fixedAssetTypes = FixedAssetType::all();
    $fixedAssetTypesWithParent = $fixedAssetTypes->map(function($fixedAssetType){
      $newFixedAssetType = $fixedAssetType;
      if($fixedAssetType->parentType_id){
        $newFixedAssetType->parent = FixedAssetType::find($fixedAssetType->parentType_id)->toArray();
      }
      return $newFixedAssetType;
    });
    return $fixedAssetTypesWithParent;
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

    $totalRecords = FixedAssetType::count();
    $recordsFiltered = $totalRecords;
    $fixedAssetTypes = FixedAssetType::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $fixedAssetTypes = $fixedAssetTypes->map(function($fixedAssetType){
      $newFixedAssetType = $fixedAssetType;
      if($fixedAssetType->parentType_id){
        $newFixedAssetType->parent = FixedAssetType::find($fixedAssetType->parentType_id)->toArray();
      }
      return $newFixedAssetType;
    });

    if($searchValue!=''){
      $fixedAssetTypes = $fixedAssetTypes->filter(function($fixedAssetType) use($searchValue){
        if (stripos($fixedAssetType, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $fixedAssetTypes->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $fixedAssetTypes);
    return $returnData;
  }

  public function store()
  {
    $fixedAssetType = Input::all();
    if(FixedAssetType::create($fixedAssetType)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $fixedAssetType = Input::all();
    $savedFixedAssetType = FixedAssetType::find($id);
    if($savedFixedAssetType->update($fixedAssetType)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify(['fixedAssetType_id', 'parentType_id'], $id, ['FixedAssetCategory', 'FixedAssetType']);
    if($canRemove === true){
      if (FixedAssetType::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'tipo', '', $modelName);
    }
  }

}
