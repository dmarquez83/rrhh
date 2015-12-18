<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\ImportTax;
use App\Models\Product;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use App\Helpers\DocumentReferenceVerificator;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for ImportTax Model
|--------------------------------------------------------------------------
*/

class ImportTaxesController extends Controller {


  public function index()
  {
    $taxes = ImportTax::with('taxType')->get();
    return $taxes;
  }

  public function store()
  {
    $billingTax = Input::all();
    if(ImportTax::create($billingTax)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $tax = Input::all();
    $savedtax = ImportTax::find($id);
    if($savedtax->update($tax)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify('import_tax_ids', $id, ['Product']);

    if($canRemove === true){
      if (ImportTax::find($id)->delete()) {
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
