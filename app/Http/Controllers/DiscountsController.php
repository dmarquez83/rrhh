<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Discount;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;


class DiscountsController extends Controller {

  public function index()
  {
    $discounts = Discount::all();
    return $discounts;
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

    $totalRecords = Discount::count();
    $recordsFiltered = $totalRecords;
    $discounts = Discount::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $discounts = $discounts->filter(function($department) use($searchValue){
        if (stripos($department, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $discounts->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $discounts);
    
    return $returnData;
  }

  public function store()
  {
    $discount = Input::all();
    $discountCreated = Discount::create($discount);
    if($discountCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $discount = Input::all();
    $savedDiscount = Discount::find($id);
    if($savedDiscount->update($discount)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify("discounts_id", $id, ['Employee']);
    if($canRemove === true){
      if ($discounts = Discount::find($id)->delete()) {
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
