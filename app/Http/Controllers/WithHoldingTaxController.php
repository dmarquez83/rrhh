<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\ResultMsgMaker;
use App\Models\WithHoldingTax;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for WithHoldingTax Model
|--------------------------------------------------------------------------
*/

class WithHoldingTaxController extends Controller {

  public function index()
  {
    $withHoldingTaxes = WithHoldingTax::all();        
    return $withHoldingTaxes;  
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

    $totalRecords = WithHoldingTax::count();
    $recordsFiltered = $totalRecords;
    $withHoldingTax = WithHoldingTax::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $withHoldingTax = $withHoldingTax->filter(function($tax) use($searchValue){
        if (stripos($tax, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $withHoldingTax->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $withHoldingTax);
    return $returnData;
  }
  
  public function store()
  {
    $newWithHoldingTax = Input::get();
  
    if(WithHoldingTax::create($newWithHoldingTax)){
      return ResultMsgMaker::saveSuccess();

    } else {
      return ResultMsgMaker::error();
    }
  }
  
  public function update($id)
  {
    $savedWithHoldingTax = WithHoldingTax::find($id);
    $newData = Input::all();
    if($savedWithHoldingTax->update($newData)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  public function destroy($id)
  {
    if(WithHoldingTax::find($id)->delete()){
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

}
