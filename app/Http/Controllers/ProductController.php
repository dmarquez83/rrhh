<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\BillingTax;
use App\Models\Product;
use App\Models\Supplier;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for Product Model
|--------------------------------------------------------------------------
*/

class ProductController extends Controller {

  public function index()
  {
    $products = Product::warehouse()
      ->with('billingTaxes')
      ->get();
    return $products;
  }

  public function temporaryStock()
  {
    $parameter = Input::all();
    $products = Product::warehouse()
      ->where('temporaryStock', '>', 0)
      ->where($parameter['parameter'], '=', $parameter['value'])
      ->select('supplier_id', 'code', 'name', 'temporaryStock')
      ->get();
    foreach ($products as $key => $product) {
      $product->distributionQuantity = 0;
      $product->remainingQuantity = $product->temporaryStock;
    }
    return $products;
  }

  public function forTable()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $supplierId = isset($params['supplier_id']) ? $params['supplier_id'] : null;
    $isPurchase = isset($params['isPurchase']) ? ($params['isPurchase'] === 'false' ? false : true): '';
    $isExpense = isset($params['isExpense']) ? ($params['isExpense'] === 'false' ? false : true): '';
    $isNational = isset($params['isNational']) && $params['isNational'] !== '' ? ($params['isNational'] === 'true' ? true : false): '';

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $supplierIds = [];
    if ($isNational !== '') {
      $findSuppliers = [];
      if ($isNational === true) {
        $findSuppliers = Supplier::where('isForeign', '=', false)->get(['_id']);
      }
      if ($isNational === false) {
        $findSuppliers = Supplier::where('isForeign', true)->get(['_id']);
      }
      foreach ($findSuppliers as $key => $findSupplierId) {
        array_push($supplierIds, $findSupplierId->_id);
      }
    }

    $totalRecords = Product::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $products = Product::warehouse()
      ->skip($start)
      ->where(function($query) use($supplierId, $isPurchase, $isExpense, $supplierIds){
        if ($supplierId) {
          $query->where('supplier_id', '=', $supplierId);
          $query->orWhere('supplier_id', '=', '');
          $query->orWhere('supplier_id', '=', null);
        }
        if (count($supplierIds) > 0) {
          $query->whereIn('supplier_id', $supplierIds);
        }

        if ($isPurchase === false) {
          $query->where('isExpenseItem', '=', false);
          $query->where('isRentItem', '=', false);
        }
        if ($isPurchase === true) {
          $query->where('isPurchaseItem', '=', true);
          $query->where('isRentItem', '=', false);
        }
        if ($isExpense === false) {
          $query->whereNotNull('supplier_id');
          $query->where('supplier_id', '!=', '');
        }
      })
      ->where(function($query) use($searchValue){
        if ($searchValue != '') {
          $query->orWhere('name', 'like', '%'.$searchValue.'%');
          $query->orWhere('code', 'like', '%'.$searchValue.'%');
          $query->orWhere('description', 'like', '%'.$searchValue.'%');
          $query->orWhere('stock', 'like', '%'.$searchValue.'%');
          $query->orWhere('unitCost', 'like', '%'.$searchValue.'%');
          $query->orWhere('salesPrice', 'like', '%'.$searchValue.'%');
        }
      })
      ->with('productsCategory', 'pricesLists', 'billingTaxes', 'importTaxes', 'supplier', 'tariffsHeading')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $products];
    return $returnData;
  }

  public function forSelectize()
  {
    $products = Product::warehouse()->select('code', 'name')->get();
    return $products;
  }

  public function massiveLoad()
  {
    $products = Input::all();

    if(is_array($products)) {

      foreach ($products as $product) {

        if(isset($product['code'])) {

          foreach ($product as $key => $value) {
            $value = str_replace("”", "\"", $value);
            $value = str_replace("“", "\"", $value);

            if ($key == 'prices_list_ids') {
              $value = trim($value);
              $value = mb_strtolower($value);
              $priceslistArray = explode(",", $value);
              $product[$key] = $priceslistArray;
            }

            if ($key == 'billing_tax_ids') {
              $value = trim($value);
              $value = mb_strtolower($value);
              $billingTaxArray = explode(",", $value);
              $newBillingTaxArray = [];
              foreach ($billingTaxArray as $keyBillTax => $billTax) {
                if ($billTax == '12') {
                  $newTax = BillingTax::where('code', '=', '0')->get(['_id']);
                  array_push($newBillingTaxArray, $newTax);
                }
                if ($billTax == '0') {
                  $newTax = BillingTax::where('code', '=', '2')->get(['_id']);
                  array_push($newBillingTaxArray, $newTax);
                }
                if ($billTax == 'no') {
                  $newTax = BillingTax::where('code', '=', '6')->get(['_id']);
                  array_push($newBillingTaxArray, $newTax);
                }
              }
              $product[$key] = $newBillingTaxArray;
            }
          }
        }
        if (!Product::create($product)) {
          return ResultMsgMaker::error();
        }
      }
    }
    return ResultMsgMaker::saveSuccess();
  }


  public function store()
  {
    $product = Input::all();
    $billingTaxes = $product['billing_tax_ids'];
    $pricesList = $product['prices_list_ids'];
    $importTaxes = $product['import_tax_ids'];
    $product['auxiliarCode'] = isset($product['auxiliarCode']) ? $product['auxiliarCode'] : $product['code'];
    $product['barsCode'] = isset($product['barsCode']) ? $product['barsCode'] : $product['code'];
    $newProduct = Product::create($product);
    if($newProduct){
      $newProduct->billingTaxes()->sync($billingTaxes);
      $newProduct->importTaxes()->sync($importTaxes);
      $newProduct->pricesLists()->sync($pricesList);
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function getBySupplier()
  {
    $parameter = Input::all();
    $code = $parameter['code'];
    $supplierId = isset($parameter['supplier_id']) ? $parameter['supplier_id'] : '';

    $product = Product::warehouse()->where('code', '=', $code)
      ->where(function($query) use($supplierId){
        if ($supplierId !== '') {
          $query->where('suplier_id', '=', $supplierId);
        }
      })
      ->with('productsCategory', 'pricesList', 'billingTaxes', 'importTaxes', 'supplier')
      ->first();
    return $product;

  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $product = Product::warehouse()->where($parameter['parameter'], '=', $parameter['value'])
      ->with('productsCategory', 'pricesLists', 'billingTaxes', 'importTaxes', 'supplier', 'tariffsHeading')->first();
    return $product;
  }

  public function show($parameterData)
  {
    $parameterName = $_GET['parameter'];
    $product = Product::warehouse()->where($parameterName, '=', $parameterData)->take(1)->get();
    return $product;
  }

  public function update($id)
  {
    $product = Input::all();
    $billingTaxes = $product['billing_tax_ids'];
    $pricesList = $product['prices_list_ids'];
    $importTaxes = $product['import_tax_ids'];
    $savedProduct = Product::find($id);
    if($savedProduct->update($product)){
      $savedProduct->billingTaxes()->sync($billingTaxes);
      $savedProduct->importTaxes()->sync($importTaxes);
      $savedProduct->pricesLists()->sync($pricesList);
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($code)
  {
    $modelList = ['SalesOffer', 'SalesOrder', 'GoodsReceipt', 'CustomerInvoice', 'PurchaseQuotation', 'PurchaseOrder', 'GoodsDelivery', 'SupplierInvoice'];
    $canRemove = DocumentReferenceVerificator::verify("products.code", $code, $modelList);

    if($canRemove === true){
      if (Product::warehouse()->where('code', '=', $code)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'producto', '', $modelName);
    }

  }

}
