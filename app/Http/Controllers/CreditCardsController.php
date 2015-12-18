<?php namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for CreditCard Model
|--------------------------------------------------------------------------
*/

class CreditCardsController extends Controller {


  public function index()
  {
    $creditCards = CreditCard::with('employee', 'ledgerAccount')->get();
    return $creditCards;
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

    $totalRecords = CreditCard::count();
    $recordsFiltered = $totalRecords;
    $creditCards = CreditCard::skip($start)
      ->with('employee')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();


    if($searchValue!=''){
      $creditCards = $creditCards->filter(function($creditCard) use($searchValue){
        if (stripos($creditCard, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $creditCards->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $creditCards);
    return $returnData;
  }

  public function store()
  {
    $creditCard = Input::all();
    if(CreditCard::create($creditCard)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $condition = Input::all();
    $savedcondition = CreditCard::find($id);
    if($savedcondition->update($condition)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $modelList = ['SupplierInvoice'];
    $canRemove = DocumentReferenceVerificator::verify(['creditCard_id'], $id, $modelList);
    if($canRemove === true){
      if (CreditCard::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'tarjeta de crédito', '', $modelName);
    }
  }
}
