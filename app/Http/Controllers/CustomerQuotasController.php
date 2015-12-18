<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\CustomerQuotas;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for CustomerQuotas Model
|--------------------------------------------------------------------------
*/

class CustomerQuotasController extends Controller {

	public function forTable()
  {
    $params = Input::all(); 

    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $customerIdentification = $params['customerIdentification'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = CustomerQuotas::count();
    $recordsFiltered = $totalRecords;
    $customerQuotas = CustomerQuotas::orderBy($columOrderName, $columOrderDir)
      ->where(function($query) use($customerIdentification){
        $query->where('customer.identification', '=', $customerIdentification);
      })
      ->skip($start)
      ->take($length)
      ->get();


    if($searchValue!=''){
      $customerQuotas = $customerQuotas->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $customerQuotas->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $customerQuotas);
    return $returnData;
  }

}
