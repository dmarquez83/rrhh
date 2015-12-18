<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\CustomerInvoice;
use App\Models\GeneralMajor;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for GeneralMajor Model
|--------------------------------------------------------------------------
*/

class GeneralMajorController extends Controller {

  public function index()
  {
    $generalMajors = GeneralMajor::with('statement');
    return $generalMajors;
  }

  public function store()
  {
    $generalMajor = Input::all();
    GeneralMajor::create($generalMajor);
  }

  public function forTable()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $startDate = isset($params['startDate']) ? $params['startDate'] : '';
    $endDate = isset($params['endDate']) ? $params['endDate'] : '';
    $accountCode = isset($params['accountCode']) ? $params['accountCode'] : '';

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = GeneralMajor::count();
    $recordsFiltered = $totalRecords;
    $generalMajor = GeneralMajor::where('accountCode', '=', $accountCode)->first();

    $startCarbonDate = new \Carbon\Carbon($startDate);
    $endCarbonDate = new \Carbon\Carbon($endDate);

    $secStartDate = new \MongoDate(strtotime($startCarbonDate->format('Y-m-d'). ' 00:00:00'));
    $secEndDate = new \MongoDate(strtotime($endCarbonDate->format('Y-m-d').' 23:59:59'));

    $finalMovements = [];
    $filterMovements = [];
    if (count($generalMajor['movements']) > 0) {
      foreach ($generalMajor['movements'] as $key => $movement) {
        $date = $movement['date']->sec;
        if ($date >= $secStartDate->sec && $date <= $secEndDate->sec) {
          $newMovement = $movement;
          $newMovement['date'] = date('Y-m-d H:i:s', $movement['date']->sec);
          array_push($finalMovements, $newMovement);
        }
      }

      $length = $length === -1 ? count($finalMovements) : $length;

      $filterMovements = array_slice($finalMovements, $start, $length);
      $filterMovements = $this->array_orderby($filterMovements, $columOrderName, $columOrderDir === 'desc' ?  SORT_DESC : SORT_ASC);
    }  

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => count($finalMovements),
      'recordsFiltered' => count($filterMovements),
      'data' => $filterMovements);
    return $returnData;
  }


  public function searchByParameters()
  {
    $searchParameters = Input::all();

    $startDate = isset($params['startDate']) ? $params['startDate'] : '';
    $endDate = isset($params['endDate']) ? $params['endDate'] : '';
    $accountCodes = isset($params['selectedAccounts']) ? $params['selectedAccounts'] : [];

    $generalMajors = GeneralMajor::all();

    $generalMajors = GeneralMajor::where(function($query) use($startDate, $endDate, $accountCodes){
        if ($startDate != '' && $endDate != ''){
          $query->whereBetween('movements.date', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if(count($accountCodes) != 0) {
          $query->whereIn('accountCode', $accountCodes);
        }
      })->get();

    return $generalMajors;
  }

  public function array_orderby()
  {
      $args = func_get_args();
      $data = array_shift($args);
      foreach ($args as $n => $field) {
          if (is_string($field)) {
              $tmp = array();
              foreach ($data as $key => $row)
                  $tmp[$key] = $row[$field];
              $args[$n] = $tmp;
              }
      }
      $args[] = &$data;
      call_user_func_array('array_multisort', $args);
      return array_pop($args);
  }

}
