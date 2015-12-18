<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Employee;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class RolLiquidationController extends Controller {

  public function index()
  {
    //
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

    $totalRecords = Employee::count();
    $recordsFiltered = Employee::count();
    $customers = Employee::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $customers = $customers->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $customers->count();
    }  

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $customers);
    return $returnData;
  }

}
