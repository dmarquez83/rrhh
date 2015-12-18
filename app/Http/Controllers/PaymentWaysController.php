<?php namespace App\Http\Controllers;

use App\Models\PaymentWay;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for PaymentWay Model
|--------------------------------------------------------------------------
*/

class PaymentWaysController extends Controller {


  public function index()
  {
    $paymentWays = PaymentWay::all();
    return $paymentWays;
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

    $totalRecords = PaymentWay::count();
    $recordsFiltered = $totalRecords;
    $paymentWays = PaymentWay::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();


    if($searchValue!=''){
      $paymentWays = $paymentWays->filter(function($paymentWay) use($searchValue){
        if (stripos($paymentWay, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $paymentWays->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $paymentWays);
    return $returnData;
  }

  public function store()
  {
    $paymentWay = Input::all();
    if(PaymentWay::create($paymentWay)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $condition = Input::all();
    $savedcondition = PaymentWay::find($id);
    if($savedcondition->update($condition)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $modelList = ['PaymentMethod'];
    $canRemove = DocumentReferenceVerificator::verify(['paymentWay_id'], $id, $modelList);
    if($canRemove === true){
      if (PaymentWay::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'forma de pago', '', $modelName);
    }
  }
}
