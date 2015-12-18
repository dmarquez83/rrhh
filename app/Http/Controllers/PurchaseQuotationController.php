<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\DocumentConfiguration;
use App\Models\PurchaseQuotation;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\CompanyInfo;
use App\Models\Supplier;
use App\Models\SalesOrder;
use App\Helpers\ResultMsgMaker;
use App\Helpers\BusinessPartner;
use App\Helpers\SystemConfiguration;
use App\Helpers\CheckPurchaseQuotationStatus;
use App\Helpers\HistoryStockProductReserveMaker;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Helpers\TemporaryKardexMaker;
use Symfony\Component\HttpFoundation\Request;
use App\Helpers\PurchaseQuotations\GenerateFromSalesOrder;
use App\Helpers\PurchaseQuotations\FilterPurchaseQuotation;
use App\Helpers\PurchaseQuotations\Distribution;
use App\Helpers\PurchaseOrder\HistoryProductReceipt;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for PurchaseQuotation Model
|--------------------------------------------------------------------------
*/

class PurchaseQuotationController extends Controller {

  public function index()
  {
    $purchaseQuotations = PurchaseQuotation::warehouse()->get();

    return $purchaseQuotations;
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
    if ($columOrderName === 'customer') {
      $columOrderName = 'customer.comercialName';
    }
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $purchaseQuotationNumberFrom = (isset($params['salesNumberFrom']) ? $params['salesNumberFrom'] : '');
    $purchaseQuotationNumberUntil = (isset($params['salesNumberUntil']) ? $params['salesNumberUntil'] : '');
    $selectedProducts = (isset($params['selectedProducts']) ? $params['selectedProducts'] : []);
    $selectedCustomers = (isset($params['selectedCustomers']) ? $params['selectedCustomers'] : []);
    $status = (isset($params['status']) ? $params['status'] : []);

    $searchValue = $params['search']['value'];

    $totalRecords = PurchaseQuotation::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $purchaseQuotations = PurchaseQuotation::warehouse()
      ->with('customer')
      ->where(function($query) use($startDate, $endDate, $status, $purchaseQuotationNumberFrom, $purchaseQuotationNumberUntil, $selectedProducts, $selectedCustomers){
        if(count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if($startDate != '' && $endDate != '') {
          $query->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if ($purchaseQuotationNumberFrom != '' && $purchaseQuotationNumberUntil != ''){
          $query->whereBetween('number', [$purchaseQuotationNumberFrom, $purchaseQuotationNumberUntil]);
        }
        if (count($selectedCustomers) > 0) {
          $query->whereIn('customerIdentification', $selectedCustomers);
        }
        if (count($selectedProducts) > 0) {
          $query->whereIn('products.code', $selectedProducts);
        }
      })
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $purchaseQuotations = $purchaseQuotations->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchaseQuotations->count();
    }

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchaseQuotations
    ];

    return $returnData;
  }

  public function purchaseQuotationWithSuppliersForTable()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $status = (isset($params['status']) ? $params['status'] : []);

    $searchValue = $params['search']['value'];

    $totalRecords = PurchaseQuotation::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $purchaseQuotations = PurchaseQuotation::warehouse()
        ->with('customer')
        ->where(function($query) use($startDate, $endDate, $status){
        if(count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if($startDate != ''&& $endDate != '') {
          $query->whereBetween('date', [$startDate, $endDate]);
        }
      })
      ->whereNotIn('status', ['Pedido generado', 'Anulado'])
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $purchaseQuotations = $purchaseQuotations->filter(function($purchaseQuotation) use($searchValue){
        if (stripos($purchaseQuotation, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchaseQuotations->count();
    }

    $finalPurchaseQuotations = [];
    foreach ($purchaseQuotations as $key => $purchaseQuotation) {
      $newPurchaseQuotation = $purchaseQuotation;
      $suppliersNames = [];
      $productNames = [];
      foreach ($purchaseQuotation['products'] as $key => $product) {
        array_push($productNames, $product['name']);
        if (isset($product['supplier_id'])) {
          $supplier = Supplier::find($product['supplier_id']);
          $businessName = isset($supplier['businessName']) ? $supplier['businessName'] : '';
          $comercialName = isset($supplier['comercialName']) ? $supplier['comercialName'] : '';
          $supplierNames = isset($supplier['names']) ? $supplier['names'] : '';
          $supplierSurnames = isset($supplier['surnames']) ? $supplier['surnames'] : '';
          $supplierCompleteName = $supplierNames.' '.$supplierSurnames;
          $finalName = '';
          $finalName = ($supplierCompleteName != '' ? $supplierCompleteName : $finalName);
          $finalName = ($comercialName != '' ? $comercialName : $finalName);
          $finalName = ($businessName != '' ? $businessName : $finalName);
          array_push($suppliersNames, $finalName);
        }
      }
      $newPurchaseQuotation['productsNames'] = $productNames;
      $newPurchaseQuotation['suppliers'] = $suppliersNames;
      array_push($finalPurchaseQuotations, $newPurchaseQuotation);
    };

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $finalPurchaseQuotations];
    return $returnData;

  }

  public function specificData()
  {
    $colums = Input::all();
    $purchaseQuotations = PurchaseQuotation::warehouse()->orderBy('number', 'asc')->get($colums);

    return $purchaseQuotations;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $purchaseQuotation = PurchaseQuotation::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $purchaseQuotation;
  }

  public function forDistribution()
  {
    $parameter = Input::all();
    $purchaseQuotations = PurchaseQuotation::warehouse()->whereIn('number', $parameter['purchaseQuotationNumbers'])
        ->whereNotNull('documentFromNumber')
        ->get();
    $purchaseQuotations = FilterPurchaseQuotation::byPartialSalesOrder($purchaseQuotations->toArray(), $parameter['supplier_id']);

    return $purchaseQuotations;
  }

  public function saveDistribution()
  {
    $finalData = Input::all();
    $purchaseQuotations = $finalData['purchaseQuotations'];
    if (Distribution::distribution($purchaseQuotations)) {
      HistoryProductReceipt::registerDistributionQuantity($purchaseQuotations, $finalData['selectedPurchaseOrderNumber']);
      return ResultMsgMaker::saveSuccess();
    }
    return ResultMsgMaker::error();
  }

  public function getAllByParameterPostForConsolidation()
  {
    $parameter = Input::all();
    $purchaseQuotations = PurchaseQuotation::warehouse()->where($parameter['parameter'], '=', $parameter['value'])
      ->whereIn('status', ['Abierto', 'Pedido parcial'])
      ->get();
    $products = $this->getProductsOfPurchaseQuotation($purchaseQuotations, $parameter['value']);
    return $products;
  }

  private function getProductsOfPurchaseQuotation($purchaseQuotations, $supplierId)
  {
    $products = [];
    foreach ($purchaseQuotations as $key => $purchaseQuotation) {
      $purchaseQuotationProducts = [];
      foreach ($purchaseQuotation['products'] as $key => $product) {
        $quantityRemaining = isset($product['quantityRemaining']) ? $product['quantityRemaining'] : $product['quantity'];
        if($quantityRemaining != 0){
          if (isset($product['supplier_id']) && $product['supplier_id'] === $supplierId) {
            $selectedProduct = $product;
            $selectedProduct['purchaseQuotationNumber'] = $purchaseQuotation['number'];
            $selectedProduct['customer'] = Customer::find($purchaseQuotation['customer_id'])->toArray();
            $selectedProduct['supplier'] = Supplier::find($product['supplier_id'])->toArray();
            $selectedProduct['selectedQuantity'] = $quantityRemaining;
            array_push($purchaseQuotationProducts, $selectedProduct);
          }
        }
      }
      $products = array_merge($products, $purchaseQuotationProducts);
    }
    return $products;
  }

  public function getPurchaseQuotationSuppliers()
  {

    $supplier_ids = [];
    $purchaseQuotations = PurchaseQuotation::warehouse()->whereIn('status', ['Abierto', 'Pedido parcial'])->get(['products']);
    foreach ($purchaseQuotations as $key => $purchaseQuotation) {
      foreach ($purchaseQuotation['products'] as $key => $product) {
        if (isset($product['supplier_id'])) {
          array_push($supplier_ids, $product['supplier_id']);
        }
      }
    }

    $supplier_ids = array_unique($supplier_ids);
    $suppliers = Supplier::whereIn('_id', $supplier_ids)->get();
    return $suppliers;
  }

  public function printDocument()
  {
    $salesOffer = PurchaseQuotation::warehouse()->where('number', Input::get('number'))->first();
    $customer = Customer::find($salesOffer['customer_id']);
    $customerName = BusinessPartner::getName($customer->toArray());
    $companyInfo = CompanyInfo::first();
    $salesOfferDetails = $salesOffer->details ? $salesOffer->details : '';
    $seller = '';
    $data = [
      'companyInfo' => $companyInfo,
      'document' => $salesOffer->toArray(),
      'documentDetails' => $salesOfferDetails,
      'customer' => $customer->toArray(),
      'customerName' => $customerName,
      'seller' => $seller,
      'products' => $salesOffer['products']
    ];

    $pdf = \PDF::loadView('pdf.purchaseQuotation', $data)->setPaper('a4');
    $path = SystemConfiguration::getPublicCompanyPath().'prints/';
    if (!\Storage::exists($path)) {
      \Storage::makeDirectory($path);
    }
    $pdf->save(SystemConfiguration::getPublicCompanyPath(true).'prints/purchaseQuotation.pdf');
    return ['url'=> SystemConfiguration::getPublicCompanyPath().'prints/purchaseQuotation.pdf'];
  }


  public function store()
  {
    $newPurchaseQuotation = Input::get();
    $newPurchaseQuotation['number'] = $this->generatePurchaseQuotationNumber();
    $customer = Customer::find($newPurchaseQuotation['customer_id']);
    $newPurchaseQuotation['customerIdentification'] = $customer->identification;
    if(PurchaseQuotation::create($newPurchaseQuotation)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  public function generateFromSalesOrder()
  {
    $salesOrder = Input::get();
    if(GenerateFromSalesOrder::generate($salesOrder)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $purchaseQuotation = PurchaseQuotation::find($id);
    $newData = Input::all();
    if($purchaseQuotation->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $purchaseQuotation = PurchaseQuotation::find($id);
    $annulDate = new \DateTime();
    $purchaseQuotation->annulDate = $annulDate->format('Y-m-d H:i:s');
    $purchaseQuotation->status = 'Anulado';
    if ($purchaseQuotation->save()) {
      if ($purchaseQuotation->documentFromNumber != null) {
        $this->revertSalesOrder($purchaseQuotation->documentFromNumber);
      }
      return ResultMsgMaker::annulSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function revertSalesOrder($documentNumber)
  {
    $document = SalesOrder::warehouse()->where('number', '=', $documentNumber)->first();
    $document->status = 'Abierto';
    $document->documentToName = null;
    $document->documentToNumber = null;
    $document->save();
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '008')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generatePurchaseQuotationNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
