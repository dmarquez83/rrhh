<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\DocumentReferenceVerificator;
use App\Models\State;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class StateController extends Controller {


  public function index()
  {
    $states = State::all();
    return $states;
  }

  public function store()
  {
    $state = Input::all();
    $stateCreated = State::create($state);
    if($stateCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $state = Input::all();
    $savedState = State::find($id);
    if($savedState->update($state)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $modelList = ['SalesOffer', 'SalesOrder', 'GoodsReceipt', 'CustomerInvoice',
      'PurchaseQuotation', 'PurchaseOrder', 'GoodsDelivery', 'SupplierInvoice'];
    $canRemove = DocumentReferenceVerificator::verify('state_id', $id, $modelList);

    if($canRemove === true){
      if (State::where('_id', '=', $id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'estado', '', $modelName);
    }
  }
}
