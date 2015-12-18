<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class CustomerAccountController extends Controller {


  public function index()
  {
    $customerAccount = CustomerAccount::with('customer')->get();
    return $customerAccount;
  }


  public function store()
  {
    $accountData = Input::all();
    $customerData = $accountData['customer'];


    $resultado = array(
        'type' => 'success',
        'msg' => 'La cuenta se ha guardado con exito');


    if($customerData['exist'] == false) {
      unset($customerData['exist']);
      $customerData['_id'] = new MongoId(Customer::create($customerData)->id);
    }

    unset($accountData['customer']);
    $accountData['customer_id'] = new MongoId($customerData['_id']);
    $accountData['status'] = 'Active';

    if (!CustomerAccount::create($accountData)) {
      $resultado['type'] = 'danger';
      $resultado['msg'] = 'Ocurrio un Problema al guardar la cuenta';
      return $resultado;
    }

    return $resultado;
  }

  public function searchByParameters()
  {

    $searchParameters = Input::all();
    $starDate = $searchParameters['startDate'];
    $endDate = $searchParameters['endDate'];
    $customers = (isset($searchParameters['selectedCustomers']) ? $searchParameters['selectedCustomers'] : array());

    $draw = $searchParameters['draw'];
    $start = $searchParameters['start'];
    $length = $searchParameters['length'];

    $columOrderIndex = $searchParameters['order'][0]['column'];
    $columOrderDir = $searchParameters['order'][0]['dir'];
    $columOrderName = $searchParameters['columns'][$columOrderIndex]['data'];

    $searchValue = $searchParameters['search']['value'];

    $totalRecords = CustomerAccount::count();
    $recordsFiltered = $totalRecords;
    $customerAccount = CustomerAccount::whereBetween('date',  array($starDate, $endDate))
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    foreach ($customerAccount as $key => $account) {
      $customer = Customer::find($account->customer_id);
      $customerAccount[$key]['customer'] = $customer; 
    }

    if (count($customers)>0){
      $customerAccount = $customerAccount->filter(function($account) use($customers)
      {
        $index = array_search($account->customer_id, $customers);
        if ($index !== false) {
          return true;
        }
 
      });
    }

    if($searchValue!=''){
      $customerAccount = $customerAccount->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $customerAccount->count();
    }  

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $customerAccount);
    return $returnData;
  }

}
