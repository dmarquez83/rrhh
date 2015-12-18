<?php namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use App\Models\DocumentConfiguration;
use App\Models\ImportOrder;
use App\Models\ImportQuotation;
use App\Models\SalesOrder;
use App\Models\GoodsReceipt;
use App\Helpers\ResultMsgMaker;
use App\Helpers\ApprovalDocument;
use App\Helpers\CheckImportQuotationStatus;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for ImportOrder Model
|--------------------------------------------------------------------------
*/

class ImportOrderController extends Controller {

  public function index()
  {
    $importQuotations = ImportOrder::warehouse()->with('supplier')->get();
    return $importQuotations;
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

    $totalRecords = ImportOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = ImportOrder::warehouse()
      ->skip($start)
      ->with('supplier')
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

  public function forApproval()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = ImportOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('007');
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');

    $importOrders = ImportOrder::warehouse()
      ->where(function($query) use($prevStatus, $approvalStatus){
        if($prevStatus != '') {
          $query->where('status', '=', $prevStatus);
        }
        $query->where('status', '!=', $approvalStatus);
      })
      ->whereNotIn('status', ['Abierto', 'Aprobado', 'Rechazado', 'Pedido enviado', 'Anulado', 'Rechazado - Anulado',
        'Ingreso mercadería', 'Ingreso parcial de mercadería', 'Recibido parcial', 'Recibido completo', 'Factura Ingresada'])
      ->skip($start)
      ->with('supplier')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $importOrders = $importOrders->filter(function($customer) use($searchValue){
        if (stripos($importOrders, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $importOrders->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $importOrders);
    return $returnData;
  }

  public function forDistribution()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = ImportOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('007');
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');

    $importOrders = ImportOrder::warehouse()
      ->whereNotNull('importQuotationNumbers')
      ->orWhereNotNull('documentFromNumber')
      ->whereIn('status', ['Recibido parcial', 'Recibido completo'])
      ->skip($start)
      ->with('supplier')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $importOrders = $this->filterImportOrderByPartialSalesOrder($importOrders->toArray());

    if($searchValue!=''){
      $importOrders = $importOrders->filter(function($importOrder) use($searchValue){
        if (stripos($importOrder, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $importOrders->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $importOrders);
    return $returnData;
  }

  public function forSupplierInvoiceForTable()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $supplierId = $params['supplier_id'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = ImportOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('007');
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');

    $importOrdernNumbers = GoodsReceipt::where('supplier_id', '=', $supplierId)->select('importOrderNumber')->get();
    $importOrdernNumbers = array_pluck($importOrdernNumbers->toArray(), 'importOrderNumber');

    $importOrders = ImportOrder::warehouse()
      ->where('supplier_id', '=', $supplierId)
      ->whereIn('number', $importOrdernNumbers)
      ->whereNotIn('status', ['Factura Ingresada'])
      ->skip($start)
      ->with('supplier')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $importOrders = $this->filterProductQuantityReceiptOfImportOrders($importOrders->toArray());

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $importOrders);
    return $returnData;

  }

  public function forSupplierInvoice()
  {
    $parameter = Input::all();
    $importOrdernNumbers = GoodsReceipt::warehouse()
      ->where('supplier_id', '=', $parameter['value'])->select('importOrderNumber')->get();

    $importOrdernNumbers = array_pluck($importOrdernNumbers->toArray(), 'importOrderNumber');

    $importOrders = ImportOrder::warehouse()
      ->whereIn('number', $importOrdernNumbers)
      ->whereNotIn('status', ['Factura Ingresada'])
      ->get();

    $importOrders = $this->filterProductQuantityReceiptOfImportOrders($importOrders->toArray());
    return $importOrders;
  }

  private function filterProductQuantityReceiptOfImportOrders($importOrders)
  {
    $filterImportOrders = [];
    foreach ($importOrders as $key => $importOrder) {
      $importOrdersProducts = [];
      foreach ($importOrder['products'] as $key => $product) {
        if (isset($product['quantityReceipt']) && $product['quantityReceipt'] > 0) {
          array_push($importOrdersProducts, $product);
        }
      }
      if (count($importOrdersProducts) > 0) {
        $importOrder['products'] = $importOrdersProducts;
        array_push($filterImportOrders, $importOrder);
      }
    }
    return $filterImportOrders;
  }

  private function filterImportOrderByPartialSalesOrder($importOrders)
  {
    $finalSelectedImportOrder = [];
    foreach ($importOrders as $key => $importOrder) {
      $importQuotations = [];
      if (isset($importOrder['importQuotationNumbers']) ) {
        $importQuotations = ImportQuotation::warehouse()->whereIn('number', $importOrder['importQuotationNumbers'])->get();
      }
      if (isset($importOrder['documentFromNumber']) ) {
        $importQuotations = ImportQuotation::warehouse()->where('number', '=', $importOrder['documentFromNumber'])->get();
      }
      $importQuotations = $this->filterImportQuotationByPartialSalesOrder($importQuotations->toArray());
      $importQuotations = $this->filterImportQuotationProductsBySupplier($importQuotations, $importOrder['supplier_id']);
      if (count($importQuotations) > 0) {
        $newImportOrder = $this->setDistributionQuantity($importOrder, $importQuotations);
        array_push($finalSelectedImportOrder, $newImportOrder);
      }
    }
    return $finalSelectedImportOrder;
  }

  private function filterImportQuotationByPartialSalesOrder($importQuotations)
  {
    $partialImportQuotations = [];
    foreach ($importQuotations as $keyImportQuotation => $importQuotation) {
      if (isset($importQuotation['documentFromNumber'])) {
        $salesOrder = SalesOrder::warehouse()->where('number', '=', $importQuotation['documentFromNumber'])->first();

        if ($salesOrder->status == 'Venta parcial' || $salesOrder->status == 'Recibido parcial'
          || $salesOrder->status == 'Solicitud de compra generada' || $salesOrder->status == 'Facturado parcial') {
          array_push($partialImportQuotations, $importQuotation);
        }
      }
    }
    return $partialImportQuotations;
  }

  private function filterImportQuotationProductsBySupplier($importQuotations, $supplierId)
  {
    $filterImportQuotations = [];
    foreach ($importQuotations as $keyImportQuotation => $importQuotation) {
      $newImportQuotation = $importQuotation;
      $newImportQuotation['products'] = [];
      foreach ($importQuotation['products'] as $key => $product) {
        if ($product['supplier_id'] === $supplierId) {
          if (isset($product['distributionQuantity']) && $product['distributionQuantity'] > 0) {
            if ($product['quantity'] !== $product['distributionQuantity']) {
              array_push($newImportQuotation['products'], $product);
            }
          } else {
            array_push($newImportQuotation['products'], $product);
          }
        }
      }

      if (count($newImportQuotation['products']) > 0) {
        array_push($filterImportQuotations, $newImportQuotation);
      }
    }
    return $filterImportQuotations;
  }

  private function setDistributionQuantity($importOrder, $importQuotations) {
    $importOrderProducts = $importOrder['products'];
    foreach ($importOrderProducts as $key => $product) {
      foreach ($importQuotations as $importQuotation) {
        $findProductKey = array_search($product['code'], array_column($importQuotation['products'], 'code'));
        if ($findProductKey !== false) {
          if (!isset($importOrderProducts[$key]['distributionQuantity'])) {
            $importOrderProducts[$key]['distributionQuantity'] = 0;
          }
          $distributionQuantity = (isset($importQuotation['products'][$findProductKey]['distributionQuantity']) ? $importQuotation['products'][$findProductKey]['distributionQuantity'] : 0);
          $importOrderProducts[$key]['distributionQuantity'] += $distributionQuantity;
        }
      }
    }
    $importOrder['products'] = $importOrderProducts;
    return $importOrder;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $importOrder = ImportOrder::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $importOrder;
  }


  public function generate()
  {
    $dataForGenerateImportOrder = Input::get();
    $prevDocument = $dataForGenerateImportOrder['prevDocument'];
    $newImportOrder = $dataForGenerateImportOrder['importOrder'];
    $newImportOrder['number'] = $this->generateImportOrderNumber();
    $prevStatus = ApprovalDocument::getApprovalsNumber('007');
    $newImportOrder['status'] = ($prevStatus == 0 ? 'Abierto' : 'Pendiente de aprobación');

    if(ImportOrder::create($newImportOrder)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->savePuchaseQuotationFromGenerate($prevDocument, $newImportOrder['number']);
      $this->updateQuantityOrderOfImportQuotationProduct($newImportOrder['products'], $newImportOrder);
      CheckImportQuotationStatus::ImportQuotation($newImportOrder['documentFromNumber']);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function savePuchaseQuotationFromGenerate($importQuotation, $importOrderNumber)
  {
    $importQuotation['status'] = 'Pedido generado';
    $importQuotation['documentToName'] = 'importOrder';
    $importQuotation['documentToNumber'] = $importOrderNumber;

    $savedImportQuotation = ImportQuotation::find($importQuotation['_id']);
    $savedImportQuotation->update($importQuotation);
  }


  private function updateQuantityOrderOfImportQuotationProduct($importOrderProducts, $importOrder)
  {

    foreach ($importOrderProducts as $key => $importOrderProduct) {

      $importQuotation = $this->getImportQuotationByPurcharOrderProduct($importOrder, $importOrderProduct);
      $importQuotationProducts = $importQuotation->products;

      $result = $this->findProduct($importOrderProduct['code'], $importQuotationProducts);
      $findProduct = $result['product'];
      $keyOfProduct = $result['key'];

      $findProduct = $this->generateProductOrderedHistory($importOrder, $importOrderProduct, $findProduct);
      $quantityOrdered = $quantityOrdered = isset($importOrderProduct['selectedQuantity']) ?
        $importOrderProduct['selectedQuantity'] : $importOrderProduct['quantity'];

      $findProduct = $this->calculateQuantityOrder($findProduct, $quantityOrdered);

      $findProduct['quantityRemaining'] = $findProduct['quantity'] - $findProduct['quantityOrdered'];
      $importQuotationProducts[$keyOfProduct] = $findProduct;
      $importQuotation->products = $importQuotationProducts;
      $importQuotation->save();

    }

  }

  private function getImportQuotationByPurcharOrderProduct($importOrder, $importOrderProduct)
  {
    $importQuotationNumber = isset($importOrderProduct['importQuotationNumber']) ?
      $importOrderProduct['importQuotationNumber'] : $importOrder['documentFromNumber'];
    $importQuotation = ImportQuotation::warehouse()->where('number', '=', $importQuotationNumber)->first();
    return $importQuotation;
  }

  private function generateProductOrderedHistory($importOrder, $importOrderProduct, $selectedProduct)
  {
    $quantityOrdered = isset($importOrderProduct['selectedQuantity']) ? $importOrderProduct['selectedQuantity'] : $importOrderProduct['quantity'];
    $newImportOrderHistory = [
      'importOrderNumber'=> $importOrder['number'],
      'date' => $importOrder['creationDate'],
      'quantityOrdered' => $quantityOrdered
    ];

    if(isset($selectedProduct['ordersHistory'])){
      array_push($selectedProduct['ordersHistory'], $newImportOrderHistory);
    } else {
      $selectedProduct['ordersHistory'] = [$newImportOrderHistory];
    }
    return $selectedProduct;
  }

  private function findProduct($productCode, $productsArray)
  {
    $keyOfProduct = array_search($productCode, array_column($productsArray, 'code'));
    $findProduct = $productsArray[$keyOfProduct];
    return ['product'=> $findProduct, 'key' => $keyOfProduct];
  }

  private function calculateQuantityOrder($findProduct, $quantityOrdered)
  {
    if(isset($findProduct['quantityOrdered'])){
      $findProduct['quantityOrdered'] += $quantityOrdered;
    } else {
      $findProduct['quantityOrdered'] = $quantityOrdered;
    }
    return $findProduct;
  }

  public function store()
  {
    $newImportOrder = Input::get();
    $newImportOrder['number'] = $this->generateImportOrderNumber();
    $prevStatus = ApprovalDocument::getApprovalsNumber('007');
    $newImportOrder['status'] = ($prevStatus == 0 ? 'Abierto' : 'Pendiente de aprobación');
    if(ImportOrder::create($newImportOrder)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function approval($id)
  {
    $importOrder = ImportOrder::find($id);
    $newData = Input::all();
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');
    $newData['status'] = $approvalStatus == '' ? 'Aprobado' : $approvalStatus;
    if($importOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function rejected($id)
  {
    $importOrder = ImportOrder::find($id);
    $newData = Input::all();
    $rejectedStatus = ApprovalDocument::getRejectedStatus('007');
    $newData['status'] = $rejectedStatus == '' ? 'Rechazado' : $rejectedStatus;
    if($importOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $importOrder = ImportOrder::find($id);
    $newData = Input::all();
    if($importOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }



  private function checkImportQuotationStatus($importQuotationNumbers)
  {
    foreach ($importQuotationNumbers as $key => $importQuotationNumber) {
      CheckImportQuotationStatus::importQuotation($importQuotationNumber);
    }
  }

  public function saveConsolidation()
  {
    $newImportOrder = Input::get();
    $newImportOrder['number'] = $this->generateImportOrderNumber();
    $selectedProducts = $newImportOrder['selectedProducts'];
    if(ImportOrder::create($newImportOrder)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->updateQuantityOrderOfImportQuotationProduct($selectedProducts, $newImportOrder);
      $this->checkImportQuotationStatus($newImportOrder['importQuotationNumbers']);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  public function destroy($id)
  {
    $savedImportOrder = ImportOrder::find($id);
    $status = $savedImportOrder->status;
    if ($status == 'Rechazado'){
      $status = 'Rechazado - Anulado';
    } else {
      $status = 'Anulado';
    }
    $fechaAnulacion = new \DateTime();
    $savedImportOrder->annulDate = $fechaAnulacion->format('Y-m-d H:i:s');
    $savedImportOrder->status = $status;
    if ($savedImportOrder->save()) {
      if($savedImportOrder->isConsolidation === true){
        $this->freeConsolidation($savedImportOrder);
        return ResultMsgMaker::annulSuccess();
      } else {
        return ResultMsgMaker::annulSuccess();
      }
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function freeConsolidation($importOrder)
  {
    $importOrderNumber = $importOrder->number;
    $importQuotationNumbers = $importOrder->importQuotationNumbers;
    foreach ($importQuotationNumbers as $key => $importQuotationNumber) {
      $importQuotation = ImportQuotation::warehouse()->where('number', '=', $importQuotationNumber)->first();
      $importQuotationProducts = $importQuotation->products;
      foreach ($importQuotationProducts as $productKey => $product) {
        if (isset($product['ordersHistory'])) {
          foreach ($product['ordersHistory'] as $historyKey => $history) {
            $isAnnuled = (isset($history['isAnnuled']) ? $history['isAnnuled'] : false);
            if ($history['importOrderNumber'] == $importOrderNumber && $isAnnuled == false) {
              $quantityOrdered = $history['quantityOrdered'];
              $historyLine = $history;
              $historyLine['isAnnuled'] = true;
              $historyLine['date'] = $importOrder->annulDate;
              $importQuotationProducts[$productKey]['quantityOrdered'] -= $quantityOrdered;
              $importQuotationProducts[$productKey]['quantityRemaining'] += $quantityOrdered;
              array_push($importQuotationProducts[$productKey]['ordersHistory'], $historyLine);
            }
          }
        }
      }
      $importQuotation->products = $importQuotationProducts;
      $importQuotation->save();
      CheckImportQuotationStatus::importQuotation($importQuotation->number);
    }
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '023')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateImportOrderNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
