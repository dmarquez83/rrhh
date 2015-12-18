<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\ModelAccountingEntries;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for ModelAccountingEntries Model
|--------------------------------------------------------------------------
*/

class ModelAccountingEntriesController extends Controller {


  public function index()
  {
    $modelAccountingEntries = ModelAccountingEntries::with('document')->get();
    return $modelAccountingEntries;
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

    $totalRecords = ModelAccountingEntries::count();
    $recordsFiltered = $totalRecords;
    $finalModels = [];

    if($searchValue!=''){
      $allModels = ModelAccountingEntries::with('document')
        ->orderBy($columOrderName, $columOrderDir)
        ->get();

      $finalModels = $allModels->filter(function($ledgerAccount) use($searchValue){
        if (stripos($ledgerAccount, $searchValue)!== false) {return true;};
        return false;
      })->values();;
      $recordsFiltered = $finalModels->count();
      $finalModels = $finalModels->slice($start, $length);
      
    } else {
      $finalModels = ModelAccountingEntries::with('document')
        ->skip($start)
        ->take($length)
        ->orderBy($columOrderName, $columOrderDir)
        ->get();
    } 

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $finalModels);
    return $returnData;
  }


  public function store()
  {
    $modelAccountingEntry = Input::all();
    if(ModelAccountingEntries::create($modelAccountingEntry)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
    
  }


  public function update($id)
  {
    $savedModel = ModelAccountingEntries::find($id);
    $newData = Input::all();
    if($savedModel->update($newData)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  public function destroy($id)
  {
    if(ModelAccountingEntries::find($id)->delete()){
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


}
