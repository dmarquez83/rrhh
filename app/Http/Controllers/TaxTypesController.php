<?php namespace App\Http\Controllers;

use App\Models\TaxType;
use App\Helpers\ResultMsgMaker;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use App\Helpers\DocumentReferenceVerificator;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for TaxType Model
|--------------------------------------------------------------------------
*/

class TaxTypesController extends Controller {


	public function index()
	{
		$taxTypes = TaxType::all();
		return $taxTypes;
	}

	public function store()
  {
    $taxType = Input::all();
    if(TaxType::create($taxType)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

	public function update($id)
	{
		$taxType = Input::all();
    $savedTaxType = TaxType::find($id);
    if($savedTaxType->update($taxType)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }

	}

	public function getByParameterPost()
  {
    $parameter = Input::all();
    $taxType = TaxType::where($parameter['parameter'], '=', $parameter['value'])->get();
    return $taxType[0];
  }

	public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify('taxType_id', $id, ['BillingTax', 'ImportTax']);

    if($canRemove === true){
      if (TaxType::find($id)->delete()) {
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
