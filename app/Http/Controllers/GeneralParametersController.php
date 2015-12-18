<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\GeneralParameter;
use App\Models\CompanyInfo;
use App\Models\PaymentWay;
use App\Models\PaymentMethod;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for GeneralParameter Model
|--------------------------------------------------------------------------
*/

class GeneralParametersController extends Controller {

	public function index()
	{
		$generalParameters = GeneralParameter::all();
		return $generalParameters;
	}

	public function store()
	{
    $parameter = Input::all();
    $parameterCreated = GeneralParameter::create($parameter);
    if($parameterCreated){
      return ResultMsgMaker::saveSuccess();
    }else {
      return ResultMsgMaker::error();
    }
	}

	public function update($id)
	{
    $parameter = Input::all();
    $savedParameter = GeneralParameter::find($id);
    if($savedParameter->update($parameter)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function getByParameterPost()
  {
    $parameter = Input::all();
    $parameter = GeneralParameter::where($parameter['parameter'], '=', $parameter['value'])->get();
    return $parameter[0];
  }

  public function configParameters()
  {
    $company = CompanyInfo::first()->companyCode;
    $warehouseSerie = Session::get('currentWarehouse')['series'];



    $configParameters = [
      'IVATaxTypeId' => GeneralParameter::where('code', 'IVA')->first()->alfanumericValue,
      'ICE' => GeneralParameter::where('code', 'ICE')->first()->alfanumericValue,
      'IRBPNR' => GeneralParameter::where('code', 'IRBPNR')->first()->alfanumericValue,
      'SAEBASIC' => GeneralParameter::where('code', 'SaeBasic')->first()->alfanumericValue,
      'SAEACCOUNTING' => GeneralParameter::where('code', 'SaeAccounting')->first()->alfanumericValue,
      'SAEINVENTORY' => GeneralParameter::where('code', 'SaeInventory')->first()->alfanumericValue,
      'FEAMBIENTE' => GeneralParameter::where('code', 'FEAmbiente')->first()->alfanumericValue === '1' ? 'PRUEBAS': 'PRODUCCIÓN',
      'FEEMISION' => 'NORMAL',
      'PAYMENTWAYS' => PaymentWay::all()->toArray(),
      'PAYMENTMETHODS' => PaymentMethod::all()->toArray(),
      'COMPANYSERIE' => $company,
      'WAREHOUSESERIE' => $warehouseSerie
    ];

    return $configParameters;
  }

	public function destroy($id)
	{
		if(GeneralParameter::find($id)->delete()){
	      return ResultMsgMaker::deleteSuccess();
	    } else {
	      return ResultMsgMaker::error();
	    }
	}

}
