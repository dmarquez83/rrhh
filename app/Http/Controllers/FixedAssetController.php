<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\FixedAsset;
use App\Helpers\ResultMsgMaker;
use App\Models\Transport;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for FixedAsset Model
|--------------------------------------------------------------------------
*/


class FixedAssetController extends Controller {


  public function index()
  {
    $fixedAssets = FixedAsset::all();
    return $fixedAssets;
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

    $totalRecords = FixedAsset::count();
    $recordsFiltered = $totalRecords;
    $fixedAssets = FixedAsset::skip($start)
      ->with('fixedAssetCategory', 'department', 'supplier', 'employee')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->whereNull('deleted_at')
      ->get();

    if($searchValue!=''){
      $fixedAssets = $fixedAssets->filter(function($fixedAsset) use($searchValue){
        if (stripos($fixedAsset, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $fixedAssets->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $fixedAssets);
    return $returnData;
  }


  public function store()
  {
    $fixedAssets = Input::all();
    if(FixedAsset::create($fixedAssets)){
      if ($fixedAssets['isLogistics']) {
        $this->createTransport($fixedAssets);
      }
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  private function createTransport($fixedAssets)
  {
    $newTransport = [];
    $newTransport['plate'] =  $fixedAssets['plate'];
    $newTransport['model'] =  $fixedAssets['model'];
    $newTransport['brand'] =  $fixedAssets['brand'];
    $savedTransport = Transport::where('plate', '=', $newTransport['plate'])->first();
    if($savedTransport){
      $savedTransport->update($newTransport);
    } else {
      Transport::create($newTransport);
    }
  }

  public function show($parameterData)
  {
    $parameterName = $_GET['parameter'];
    $fixedAsset = FixedAsset::where($parameterName, '=', $parameterData)->first();
    return $fixedAsset;
  }

  public function update($id)
  {
    $savedFixedAsset = FixedAsset::find($id);
    $newData = Input::all();
    if($savedFixedAsset->update($newData)){
      if ($savedFixedAsset['isLogistics'] === true) {
        $this->createTransport($savedFixedAsset);
      }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = true;
    if($canRemove === true){
      if (FixedAsset::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'activo fijo', '', $modelName);
    }
  }

}
