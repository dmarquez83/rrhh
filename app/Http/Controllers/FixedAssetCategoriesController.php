<?php namespace App\Http\Controllers;

use App\Models\FixedAssetCategory;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for FixedAssetCategory Model
|--------------------------------------------------------------------------
*/


class FixedAssetCategoriesController extends Controller {


  public function index()
  {
    $fixedAssetCategories = FixedAssetCategory::with('fixedAssetType')->get();
    return $fixedAssetCategories;
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

    $totalRecords = FixedAssetCategory::count();
    $recordsFiltered = $totalRecords;
    $fixedAssetCategories = FixedAssetCategory::skip($start)
      ->with('fixedAssetType')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $fixedAssetCategories = $fixedAssetCategories->map(function($fixedAssetCategory){
    $newFixedAssetCategory = $fixedAssetCategory;

      return $newFixedAssetCategory;
    });

    if($searchValue!=''){
      $fixedAssetCategories = $fixedAssetCategories->filter(function($fixedAssetCategory) use($searchValue){
        if (stripos($fixedAssetCategory, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $fixedAssetCategories->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $fixedAssetCategories);
    return $returnData;
  }

  public function store()
  {
    $fixedAssetCategory = Input::all();
    if(FixedAssetCategory::create($fixedAssetCategory)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $category = Input::all();
    $savedcategory = FixedAssetCategory::find($id);
    if($savedcategory->update($category)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify(['fixedAssetCategory_id'], $id, ['FixedAsset']);
    if($canRemove === true){
      if (FixedAssetCategory::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'categoría', '', $modelName);
    }
  }
}
