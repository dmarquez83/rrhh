<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Supplier;
use App\Models\Province;
use App\Models\Bank;
use App\Models\Statement;
use App\Helpers\ResultMsgMaker;
use App\Helpers\LedgerAccountMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Supplier Model
|--------------------------------------------------------------------------
*/

class SupplierController extends Controller {

  public function index()
  {
    $suppliers = Supplier::all();
    return $suppliers;
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
    if ($columOrderName === 'supplier' || $columOrderName === '') {
      $columOrderName = 'comercialName';
    }
    $searchValue = $params['search']['value'];

    $totalRecords = Supplier::count();
    $recordsFiltered = $totalRecords;

    $suppliers = Supplier::where(function($query) use($searchValue){
        if ($searchValue != '') {
          $query->orWhere('identification', 'like', '%'.$searchValue.'%');
          $query->orWhere('businessName', 'like', '%'.$searchValue.'%');
          $query->orWhere('names', 'like', '%'.$searchValue.'%');
          $query->orWhere('surnames', 'like', '%'.$searchValue.'%');
          $query->orWhere('comercialName', 'like', '%'.$searchValue.'%');
          $query->orWhere('address', 'like', '%'.$searchValue.'%');
          $query->orWhere('telephones', 'like', '%'.$searchValue.'%');
          $query->orWhere('cellphones', 'like', '%'.$searchValue.'%');
          $query->orWhere('emails', 'like', '%'.$searchValue.'%');
        }
      })
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->whereNull('deleted_at')
      ->get();

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $suppliers->count(),
      'data' => $suppliers
    ];

    return $returnData;

  }

  public function forSelectize()
  {
    $parameter = Input::all();
    $isForeign = isset($parameter['isForeign']) ? $parameter['isForeign'] : '';
    $isNational = isset($parameter['isNational']) ? $parameter['isNational'] : '';
    $suppliers = Supplier::orderBy('names', 'asc')
      ->where(function($query)  use ($isForeign, $isNational) {
        if ($isForeign != '') {
          $query->where('isForeign', '=', true);
        }
        if ($isNational != '') {
          $query->where('isForeign', '=', false);
        }
      })
      ->get();
    return $suppliers;
  }

  public function getByProductsIds()
  {
    $parameter = Input::all();
    $suppliers = Supplier::whereIn('_id',$parameter['value'])->get();
    return $suppliers;
  }

  public function store()
  {
    $supplier = Input::all();
    if(!isset($supplier['accountsLedgerAccount_id'])){
      $accountName = $this->getNameLedgerAccount($supplier);
      $supplier['accountsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '2.1.14');
      $supplier['documentsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '2.1.13');
    }
    if(Supplier::create($supplier)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }

  }

  private function getNameLedgerAccount($supplier)
  {
    $accountName = '';
    if (isset($supplier['comercialName'])) {
      $accountName = $supplier['comercialName'];
    } else if (isset($supplier['businessName'])) {
      $accountName = $supplier['businessName'];
    } else {
      $accountName = $supplier['names'].' '.$supplier['surnames'];
    }
    $accountName .= ' '.$supplier['identification'];
    return $accountName;
  }


  public function masiveLoad()
  {
    $suppliers = Input::all();

    if(is_array($suppliers)) {

      foreach ($suppliers as $supplier) {

        if(isset($supplier['identification'])) {

          foreach ($supplier as $key => $value) {
            $value = str_replace("-", "", $value);
            $value = str_replace("”", "\"", $value);
            $value = str_replace("“", "\"", $value);
            $value = str_replace("–", "", $value);

            if ($key != 'emails' && !is_array($value)) {
              $value = trim($value);
              $supplier[$key] = $value;
            }

            if ($key == 'emails' && !is_array($value)) {
              $value = trim($value);
              $value = mb_strtolower($value);
              $emailArray = explode(",", $value);
              $supplier[$key] = $emailArray;
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
              $supplier[$key] = $telephonesArray;
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
              $supplier[$key] = $newCellphonesArray;
            }

            if(isset($supplier['provinceCode'])) {
              $province = Province::where('code', '=', $supplier['provinceCode'])->first();

              if ($key == 'provinceCode') {
                $provinceName = $province->name;
                $supplier['provinceName'] = $provinceName;
              }

              if ($key == 'cantonCode') {
                $cantonkey = array_search($value, array_column($province['cantons'], 'code'));
                $cantonName = $province->cantons[$cantonkey]['name'];
                $supplier['cantonName'] = $cantonName;
              }

              if ($key == 'parishCode') {
                $cantonkey = array_search($supplier['cantonCode'], array_column($province['cantons'], 'code'));
                $parishKey = array_search($value, array_column($province['cantons'][$cantonkey]['parishes'], 'code'));
                $parishName = $province['cantons'][$cantonkey]['parishes'][$parishKey]['name'];
                $supplier['parishName'] = $parishName;
              }
            }

            if ($key == 'isAgentIVARetention' || $key == 'isAgentIRFRetention' || $key == 'isSpecialContributor' || $key == 'isPublicCompany' || $key == 'isPassport' || $key == 'isForeign') {
              $supplier[$key] = false;
              if ($value == '1') {
                $supplier[$key] = true;
              }
            }
          }
        }

        $accountName = $this->getNameLedgerAccount($supplier);
        $supplier['accountsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '1.1.07');
        $supplier['documentsLedgerAccount_id'] = LedgerAccountMaker::generateChild($accountName, '1.1.06');

        if (!Supplier::create($supplier)) {
          return ResultMsgMaker::error();
        }

      }

    }

    return ResultMsgMaker::saveSuccess();

  }


  public function show($parameterData)
  {
    $parameterName = $_GET['parameter'];
    $supplier = Supplier::where($parameterName, '=', $parameterData)->first();
    return $supplier;
  }

  public function specificData()
  {
    $colums = Input::all();
    $suppliers = Supplier::all($colums);
    return $suppliers;
  }

  public function update($id)
  {
    $savedSupplier = Supplier::find($id);
    $newData = Input::all('Supplier');
    if($savedSupplier->update($newData['Supplier'][0])){
       return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $modelList = ['Product', 'SupplierInvoice'];
    $canRemove = DocumentReferenceVerificator::verify("supplier_id", $id, $modelList);

    if($canRemove === true){
      $supplier = Supplier::find($id);
      $accountsLedgerAccount = $supplier->accountsLedgerAccount_id;
      $documentsLedgerAccount = $supplier->documentsLedgerAccount_id;

        if ($supplier->delete()) {
          Statement::find($accountsLedgerAccount)->delete();
          Statement::find($documentsLedgerAccount)->delete();
          return ResultMsgMaker::deleteSuccess();
        } else {
          return ResultMsgMaker::error();
        }

    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);
      return ResultMsgMaker::errorCannotDelete('el', 'proveedor', '', $modelName);
    }
  }


}
