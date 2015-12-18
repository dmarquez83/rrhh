<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\SupplierPay;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SupplierPay Model
|--------------------------------------------------------------------------
*/

class SupplierPaysController extends Controller {

	public function forTable()
  {
    $params = Input::all(); 

    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $startDate = isset($params['startDate']) ? $params['startDate'] : '';
    $endDate = isset($params['endDate']) ? $params['endDate'] : '';
    $supplierIds = isset($params['supplierIds']) ? $params['supplierIds'] : [];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = SupplierPay::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $supplierPays = SupplierPay::warehouse()
      ->orderBy($columOrderName, $columOrderDir)
      ->with('supplier')
      ->where(function($query) use($startDate, $endDate, $supplierIds){
        if($startDate != '' && $endDate != ''){
          $query->whereBetween('expireDate', [$startDate, $endDate]);
        }
        if (count($supplierIds) > 0) {
          $query->whereIn('supplier_id', $supplierIds);
        }
      })
      ->skip($start)
      ->take($length)
      ->get();


    if($searchValue!=''){
      $supplierPays = $supplierPays->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $supplierPays->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $supplierPays);
    return $returnData;
  }

  public function registerExtension()
  {
    $supplierPay = Input::all();
    $savedSupplierPay = SupplierPay::find($supplierPay['_id']);
    $extensionHistory = $savedSupplierPay->extensionHistory ? $savedSupplierPay->extensionHistory : [];
    $creationDate = new \DateTime();
    $newHistory = ['date' => $savedSupplierPay['expireDate'], 'changeDate' => $creationDate->format('Y-m-d H:i:s'),
    'comment' => isset($supplierPay['comment']) ? $supplierPay['comment'] : null];
    array_push($extensionHistory, $newHistory);
    $savedSupplierPay->extensionHistory = $extensionHistory;
    $savedSupplierPay->expireDate = $supplierPay['newPayDate'];
    if($savedSupplierPay->save()){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }


}
