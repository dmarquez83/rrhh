<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Province;
use App\Models\Statement;
use App\Models\GeneralParameter;
use App\Helpers\ResultMsgMaker;
use App\Helpers\LedgerAccountMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Customer Model
|--------------------------------------------------------------------------
*/

class CustomerController extends Controller {


  public function index()
  {
    $customers = Customer::all();
    return $customers;
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

    if ($columOrderName === 'customer' || $columOrderName === '') {
      $columOrderName = 'comercialName';
    }


    $searchValue = $params['search']['value'];

    $totalRecords = Customer::count();
    $recordsFiltered = $totalRecords;
    $customers = Customer::skip($start)
      ->where(function($query) use($searchValue){
        if ($searchValue != '') {
          $query->orWhere('identification', 'like', '%'.$searchValue.'%');
          $query->orWhere('customerType', 'like', '%'.$searchValue.'%');
          $query->orWhere('names', 'like', '%'.$searchValue.'%');
          $query->orWhere('surnames', 'like', '%'.$searchValue.'%');
          $query->orWhere('comercialName', 'like', '%'.$searchValue.'%');
          $query->orWhere('address', 'like', '%'.$searchValue.'%');
          $query->orWhere('telephones', 'like', '%'.$searchValue.'%');
          $query->orWhere('cellphones', 'like', '%'.$searchValue.'%');
          $query->orWhere('emails', 'like', '%'.$searchValue.'%');
        }
      })
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->whereNull('deleted_at')
      ->get();

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $customers->count(),
      'data' => $customers);
    return $returnData;
  }

  public function forTableWithOutFinalCustomer()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = Customer::count();
    $recordsFiltered = $totalRecords;
    $customers = Customer::skip($start)
      ->where(function($query) use($searchValue){
        if ($searchValue != '') {
          $query->orWhere('identification', 'like', '%'.$searchValue.'%');
          $query->orWhere('customerType', 'like', '%'.$searchValue.'%');
          $query->orWhere('names', 'like', '%'.$searchValue.'%');
          $query->orWhere('surnames', 'like', '%'.$searchValue.'%');
          $query->orWhere('comercialName', 'like', '%'.$searchValue.'%');
          $query->orWhere('address', 'like', '%'.$searchValue.'%');
          $query->orWhere('telephones', 'like', '%'.$searchValue.'%');
          $query->orWhere('cellphones', 'like', '%'.$searchValue.'%');
          $query->orWhere('emails', 'like', '%'.$searchValue.'%');
        }
      })
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->where('identification', '!=', '9999999999999')
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

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $customer = Customer::where($parameter['parameter'], '=', $parameter['value'])->first();
    return $customer;
  }

  public function withHaveSalesOrder()
  {
    $salesOrders = SalesOrder::warehouse()
      ->where('products.distributionQuantity', '>', 0)
      ->whereIn('status', ['Recibido completo', 'Recibido parcial', 'Venta parcial'])
      ->select('customer_id', 'products')
      ->get();

    $customersIds = [];
    $salesOrders = $salesOrders->toArray();
    foreach ($salesOrders as $key => $salesOrder) {
      foreach ($salesOrder['products'] as $key => $product) {
        $soldQuantity = isset($product['soldQuantity']) ? $product['soldQuantity'] : 0;
        if (isset($product['distributionQuantity'])) {
          if ($soldQuantity < $product['distributionQuantity']) {
            array_push($customersIds, $salesOrder['customer_id']);
          }
        }
      }
    }

    $customers = [];
    if(count($customersIds) > 0) {
      $customers = Customer::whereIn('_id', $customersIds)->get();
    }
    return $customers;
  }


  public function store()
  {
    $customer = Input::all();
    if(!isset($customer['ledgerAccount_id'])){
      $accountName = $this->getNameLedgerAccount($customer);
      $customer['accountsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '1.1.07');
      $customer['documentsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '1.1.06');
    }
    if(Customer::create($customer)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }

  }

  private function getNameLedgerAccount($customer)
  {
    $accountName = '';
    if (isset($customer['comercialName'])) {
      $accountName = $customer['comercialName'];
    } else if (isset($customer['businessName'])) {
      $accountName = $customer['businessName'];
    } else {
      $accountName = $customer['names'].' '.$customer['surnames'];
    }
    $accountName .= ' '.$customer['identification'];
    return $accountName;
  }

  public function masiveLoad()
  {
    $customers = Input::all();

    if(is_array($customers)) {

      foreach ($customers as $customer) {

          if(isset($customer['identification'])) {

            foreach ($customer as $key => $value) {
              $value = str_replace("-", "", $value);
              $value = str_replace("”", "\"", $value);
              $value = str_replace("“", "\"", $value);
              $value = str_replace("–", "", $value);

              if ($key != 'emails' && !is_array($value)) {
                $value = trim($value);
                $customer[$key] = $value;
              }

              if ($key == 'emails' && !is_array($value)) {
                $value = trim($value);
                $value = mb_strtolower($value);
                $emailArray = explode(",", $value);
                $customer[$key] = $emailArray;
              }

              if ($key == 'telephones' && !is_array($value)) {
                $value = trim($value);
                $value = mb_strtolower($value);
                $telephonesArray = explode(",", $value);
                foreach ($telephonesArray as $keyTelephone => $telephone) {
                  if (strlen($telephone) == 7) {
                    $telephonesArray[$keyTelephone] = '02' . $telephone;
                  }
                }
                $customer[$key] = $telephonesArray;
              }

              if ($key == 'cellphones' && !is_array($value)) {
                $value = trim($value);
                $value = mb_strtolower($value);
                $cellphonesArray = explode(",", $value);
                $newCellphonesArray = [];
                foreach ($cellphonesArray as $keyCellphones => $cellphone) {
                  if (strlen($cellphone) == 10) {
                    $newCellphone = '593' . substr($cellphone, 1, strlen($cellphone));
                    array_push($newCellphonesArray, $newCellphone);
                  } else if (strlen($cellphone) == 9) {
                    $newCellphone = '5939' . substr($cellphone, 1, strlen($cellphone));
                    array_push($newCellphonesArray, $newCellphone);
                  }
                }
                $customer[$key] = $newCellphonesArray;
              }

              if(isset($customer['provinceCode'])) {
                $province = Province::where('code', '=', $customer['provinceCode'])->first();

                if ($key == 'provinceCode') {
                  $provinceName = $province->name;
                  $customer['provinceName'] = $provinceName;
                }

                if ($key == 'cantonCode') {
                  $cantonkey = array_search($value, array_column($province['cantons'], 'code'));
                  $cantonName = $province->cantons[$cantonkey]['name'];
                  $customer['cantonName'] = $cantonName;
                }

                if ($key == 'parishCode') {
                  $cantonkey = array_search($customer['cantonCode'], array_column($province['cantons'], 'code'));
                  $parishKey = array_search($value, array_column($province['cantons'][$cantonkey]['parishes'], 'code'));
                  $parishName = $province['cantons'][$cantonkey]['parishes'][$parishKey]['name'];
                  $customer['parishName'] = $parishName;
                }
              }

              if ($key == 'isAgentIVARetention' || $key == 'isAgentIRFRetention' || $key == 'isSpecialContributor' || $key == 'isPublicCompany' || $key == 'isPassport') {
                $customer[$key] = false;
                if ($value == '1') {
                  $customer[$key] = true;
                }
              }
            }
          }

          $accountName = $this->getNameLedgerAccount($customer);
          $customer['accountsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '1.1.07');
          $customer['documentsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '1.1.06');

          if (!Customer::create($customer)) {
            return ResultMsgMaker::error();
          }
      }
    }
    return ResultMsgMaker::saveSuccess();
  }


  public function show($parameterData)
  {
    $parameterName = $_GET['parameter'];
    $customer = Customer::where($parameterName, '=', $parameterData)->first();
    return $customer;
  }


  public function update($id)
  {
    $newData = Input::all();
    $savedCustomer = Customer::find($id);

    if($savedCustomer->update($newData)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function creditDocument()
  {
    $newData = Input::all();
    $savedCustomer = Customer::find($newData['_id']);
    $folderPath =  public_path().'/customerCreditDocuments/';
    $savedCustomer->maximunCreditAmount = isset($newData['maximunCreditAmount']) ? $newData['maximunCreditAmount'] : 0;
    $savedCustomer->observations = isset($newData['observations']) ? $newData['observations'] : '';

    if (isset($newData['identificationDocument'])){
      $document = $newData['identificationDocument'];
      $document = str_replace('data:application/pdf;base64,','',$document);
      $document = base64_decode($document);
      $absolutePath = $folderPath.$newData['identification']."_identification.pdf";
      $relativePath = '/customerCreditDocuments/'.$newData['identification']."_identification.pdf";
      $fp = fopen($absolutePath, 'w');
      fwrite($fp, $document);
      fclose($fp);

      $savedCustomer['identificationDocument_url'] = $relativePath;
    }

    if(isset($newData['rucDocument'])){
      $document = $newData['rucDocument'];
      $document = str_replace('data:application/pdf;base64,','',$document);
      $document = base64_decode($document);
      $absolutePath = $folderPath.$newData['identification']."_ruc.pdf";
      $relativePath = '/customerCreditDocuments/'.$newData['identification']."_ruc.pdf";
      $fp = fopen($absolutePath, 'w');
      fwrite($fp, $document);
      fclose($fp);

      $savedCustomer->rucDocument_url = $relativePath;
    }

    if(isset($newData['bankCertificate'])){
      $document = $newData['bankCertificate'];
      $document = str_replace('data:application/pdf;base64,','',$document);
      $document = base64_decode($document);
      $absolutePath = $folderPath.$newData['identification']."_bankCertificate.pdf";
      $relativePath = '/customerCreditDocuments/'.$newData['identification']."_bankCertificate.pdf";
      $fp = fopen($absolutePath, 'w');
      fwrite($fp, $document);
      fclose($fp);

      $savedCustomer->bankCertificate_url = $relativePath;
    }

    if(isset($newData['creditApplication'])){
      $document = $newData['creditApplication'];
      $document = str_replace('data:application/pdf;base64,','',$document);
      $document = base64_decode($document);
      $absolutePath = $folderPath.$newData['identification']."_creditApplication.pdf";
      $relativePath = '/customerCreditDocuments/'.$newData['identification']."_creditApplication.pdf";
      $fp = fopen($absolutePath, 'w');
      fwrite($fp, $document);
      fclose($fp);

      $savedCustomer->creditApplication_url = $relativePath;
    }

    if(isset($newData['promissory'])){
      $document = $newData['promissory'];
      $document = str_replace('data:application/pdf;base64,','',$document);
      $document = base64_decode($document);
      $absolutePath = $folderPath.$newData['identification']."_promissory.pdf";
      $relativePath = '/customerCreditDocuments/'.$newData['identification']."_promissory.pdf";
      $fp = fopen($absolutePath, 'w');
      fwrite($fp, $document);
      fclose($fp);

      $savedCustomer->promissory_url = $relativePath;
    }

    if($savedCustomer->save()){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  public function destroy($id)
  {
    $modelList = ['SalesOffer', 'SalesOrder', 'CustomerInvoice', 'GoodsDelivery'];
    $canRemove = DocumentReferenceVerificator::verify("customer_id", $id, $modelList);

    if($canRemove === true){
      $customer = Customer::find($id);
      $accountsLedgerAccount = $customer->accountsLedgerAccount_id;
      $documentsLedgerAccount = $customer->documentsLedgerAccount_id;
      if ($customer->delete()) {
        Statement::find($accountsLedgerAccount)->delete();
        Statement::find($documentsLedgerAccount)->delete();
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }

    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);
      return ResultMsgMaker::errorCannotDelete('el', 'cliente', '', $modelName);
    }
  }

  public function uploadCreditFiles()
  {
    $file = Input::file('file');
    $customerIdentification = Input::get('identification');
    $documentType = Input::get('type');
    $extension = $file ->getClientOriginalExtension();
    $name = $customerIdentification."_".$documentType."."."$extension";
    $file->move(public_path()."/customerCreditDocuments/", $name);

  }


}
