<?php namespace App\Http\Controllers;

use App\Models\BillingTax;
use App\Models\Product;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use App\Helpers\DocumentReferenceVerificator;

/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for BillingTax Model
|--------------------------------------------------------------------------
*/

class BillingTaxesController extends Controller {


  public function index()
  {
    $taxes = BillingTax::with('taxType')->get();
    return $taxes;
  }

  public function store()
  {
    $billingTax = Input::all();
    if(BillingTax::create($billingTax)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $tax = Input::all();
    $savedtax = BillingTax::find($id);
    if($savedtax->update($tax)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify('billing_tax_ids', $id, ['Product']);

    if($canRemove === true){
      if (BillingTax::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'impuesto', '', $modelName);
    }
  }
}
