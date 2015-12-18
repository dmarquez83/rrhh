<?php namespace App\Http\Controllers;

use App\Models\PaymentCondition;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for PaymentCondition Model
|--------------------------------------------------------------------------
*/

class PaymentConditionsController extends Controller {


  public function index()
  {
    $paymentConditions = PaymentCondition::all();
    return $paymentConditions;
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

    $totalRecords = PaymentCondition::count();
    $recordsFiltered = $totalRecords;
    $paymentConditions = PaymentCondition::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();


    if($searchValue!=''){
      $paymentConditions = $paymentConditions->filter(function($paymentCondition) use($searchValue){
        if (stripos($paymentCondition, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $paymentConditions->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $paymentConditions);
    return $returnData;
  }

  public function store()
  {
    $paymentCondition = Input::all();
    if(PaymentCondition::create($paymentCondition)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $condition = Input::all();
    $savedcondition = PaymentCondition::find($id);
    if($savedcondition->update($condition)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $modelList = ['CustomerInvoice','SupplierInvoice'];
    $canRemove = DocumentReferenceVerificator::verify(['paymentCondition_id'], $id, $modelList);
    if($canRemove === true){
      if (PaymentCondition::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'condición de Pago', '', $modelName);
    }
  }
}
