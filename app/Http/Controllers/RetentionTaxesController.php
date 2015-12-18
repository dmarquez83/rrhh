<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\RentTaxes;
use App\Models\WithHoldingTax;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for RentTaxes Model
|--------------------------------------------------------------------------
*/

class RetentionTaxesController extends Controller {

	public function index()
	{
		 $rentTaxes = RentTaxes::all();
        $wihHoldingTaxes = WithHoldingTax::all();
        $taxes = $rentTaxes->merge($wihHoldingTaxes);
        
        return $taxes;
        
	}

}
