<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\RentTaxes;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for RentTaxes Model
|--------------------------------------------------------------------------
*/

class RentTaxesController extends Controller {

  public function index()
  {
    $rentTaxes = RentTaxes::all();        
    return $rentTaxes;  
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

    $totalRecords = RentTaxes::count();
    $recordsFiltered = $totalRecords;
    $rentTaxes = RentTaxes::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $rentTaxes = $rentTaxes->filter(function($tax) use($searchValue){
        if (stripos($tax, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $rentTaxes->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $rentTaxes);
    return $returnData;
  }
  
  public function store()
  {
    $newRentTax = Input::get();
  
    if(RentTaxes::create($newRentTax)){
      return ResultMsgMaker::saveSuccess();

    } else {
      return ResultMsgMaker::error();
    }
  }
  
  public function update($id)
  {
    $savedTax = RentTaxes::find($id);
    $newData = Input::all();
    if($savedTax->update($newData)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  public function destroy($id)
  {
    if(RentTaxes::find($id)->delete()){
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


}
