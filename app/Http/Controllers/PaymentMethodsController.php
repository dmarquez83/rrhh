<?php namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for PaymentMethod Model
|--------------------------------------------------------------------------
*/


class PaymentMethodsController extends Controller {


  public function index()
  {
    $paymentMethods = PaymentMethod::all();
    return $paymentMethods;
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

    $totalRecords = PaymentMethod::count();
    $recordsFiltered = $totalRecords;
    $paymentMethods = PaymentMethod::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();


    if($searchValue!=''){
      $paymentMethods = $paymentMethods->filter(function($paymentMethod) use($searchValue){
        if (stripos($paymentMethod, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $paymentMethods->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $paymentMethods);
    return $returnData;
  }

  public function store()
  {
    $paymentMethod = Input::all();
    if(PaymentMethod::create($paymentMethod)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $condition = Input::all();
    $savedcondition = PaymentMethod::find($id);
    if($savedcondition->update($condition)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    //$modelList = ['PaymentMethod'];
    //$canRemove = DocumentReferenceVerificator::verify(['paymentMethod_id'], $id, $modelList);
    $canRemove = true;
    if($canRemove === true){
      if (PaymentMethod::find($id)->delete()) {
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
