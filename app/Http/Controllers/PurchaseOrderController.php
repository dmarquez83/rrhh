<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Models\DocumentConfiguration;
use App\Models\PurchaseOrder;
use App\Models\PurchaseQuotation;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\DocumentApprovalFlow;
use App\Models\GoodsReceipt;
use App\Models\CompanyInfo;
use App\Helpers\ResultMsgMaker;
use App\Helpers\BusinessPartner;
use App\Helpers\SystemConfiguration;
use App\Helpers\ApprovalDocument;
use App\Helpers\CheckPurchaseQuotationStatus;
use App\Helpers\PurchaseQuotations\RegisterOrderProducts as PurchaseQuotationRegisterOrderProducts;
use App\Helpers\PurchaseQuotations\Consolidations;
use App\Helpers\PurchaseQuotations\RevertOrders;
use App\Helpers\PurchaseOrder\FilterPurchaseOrder;
use App\Helpers\App\Helpers\PurchaseOrder\Mailing;
use Illuminate\Support\Facades\Session;


/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for PurchaseOrder Model
|--------------------------------------------------------------------------
*/

class PurchaseOrderController extends Controller {

  public function index()
  {
    $purchaseQuotations = PurchaseOrder::warehouse()->with('supplier')->get();
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
    if ($columOrderName === 'supplier') {
      $columOrderName = 'supplier.comercialName';
    }
    $purchaseOrderNumberFrom = (isset($params['salesNumberFrom']) ? $params['salesNumberFrom'] : '');
    $purchaseOrderNumberUntil = (isset($params['salesNumberUntil']) ? $params['salesNumberUntil'] : '');
    $selectedProducts = (isset($params['selectedProducts']) ? $params['selectedProducts'] : []);
    $selectedSuppliers = (isset($params['selectedSuppliers']) ? $params['selectedSuppliers'] : []);
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $status = (isset($params['status']) ? $params['status'] : []);

    $searchValue = $params['search']['value'];

    $totalRecords = PurchaseOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $purchaseOrders = PurchaseOrder::warehouse()
      ->where(function($query) use($startDate, $endDate, $status, $purchaseOrderNumberFrom, $purchaseOrderNumberUntil, $selectedProducts, $selectedSuppliers){
        if(count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if($startDate != '' && $endDate != '') {
          $query->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if ($purchaseOrderNumberFrom != '' && $purchaseOrderNumberUntil != ''){
          $query->whereBetween('number', [$purchaseOrderNumberFrom, $purchaseOrderNumberUntil]);
        }
        if (count($selectedSuppliers) > 0) {
          $query->whereIn('supplierIdentification', $selectedSuppliers);
        }
        if (count($selectedProducts) > 0) {
          $query->whereIn('products.code', $selectedProducts);
        }
      })
      ->skip($start)
      ->with('supplier')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $purchaseOrders = $purchaseOrders->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchaseOrders->count();
    }

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchaseOrders
    ];
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

    $totalRecords = PurchaseOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('007');
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');

    $purchaseOrders = PurchaseOrder::warehouse()
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
      $purchaseOrders = $purchaseOrders->filter(function($customer) use($searchValue){
        if (stripos($purchaseOrders, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchaseOrders->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchaseOrders);
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

    $totalRecords = PurchaseOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('007');
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');

    $purchaseOrders = PurchaseOrder::warehouse()
      ->where(function($query){
        $query->orWhereNotNull('purchaseQuotationNumber');
        $query->orWhereNotNull('purchaseQuotationNumbers');
      })
      ->whereIn('status', ['Recibido parcial', 'Recibido completo'])
      ->with('supplier')
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $purchaseOrders = FilterPurchaseOrder::byPartialSalesOrder($purchaseOrders->toArray());

    if($searchValue!=''){
      $purchaseOrders = $purchaseOrders->filter(function($purchaseOrder) use($searchValue){
        if (stripos($purchaseOrder, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchaseOrders->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchaseOrders);
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

    $totalRecords = PurchaseOrder::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('007');
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');

    $purchaseOrdernNumbers = GoodsReceipt::warehouse()->where('supplier_id', '=', $supplierId)->select('purchaseOrderNumber')->get();
    $purchaseOrdernNumbers = array_pluck($purchaseOrdernNumbers->toArray(), 'purchaseOrderNumber');

    $purchaseOrders = PurchaseOrder::warehouse()
      ->where('supplier_id', '=', $supplierId)
      ->whereIn('number', $purchaseOrdernNumbers)
      ->whereNotIn('status', ['Factura Ingresada'])
      ->skip($start)
      ->with('supplier')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $purchaseOrders = $this->filterProductQuantityReceiptOfPurchaseOrders($purchaseOrders->toArray());

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchaseOrders);
    return $returnData;

  }

  public function forSupplierInvoice()
  {
    $parameter = Input::all();
    $purchaseOrdernNumbers = GoodsReceipt::warehouse()
      ->where('supplier_id', '=', $parameter['value'])->select('purchaseOrderNumber')->get();

    $purchaseOrdernNumbers = array_pluck($purchaseOrdernNumbers->toArray(), 'purchaseOrderNumber');

    $purchaseOrders = PurchaseOrder::warehouse()
      ->whereIn('number', $purchaseOrdernNumbers)
      ->whereNotIn('status', ['Factura Ingresada'])
      ->get();

    $purchaseOrders = $this->filterProductQuantityReceiptOfPurchaseOrders($purchaseOrders->toArray());
    return $purchaseOrders;
  }

  public function specificData()
  {
    $colums = Input::all();
    $purchaseOrders = PurchaseOrder::warehouse()->orderBy('number', 'asc')->get($colums);
    return $purchaseOrders;
  }

  public function printDocument()
  {
    $purchaseOrder = PurchaseOrder::warehouse()->where('number', Input::get('number'))->first();
    $supplier = Supplier::find($purchaseOrder['supplier_id']);
    $supplierName = BusinessPartner::getName($supplier->toArray());
    $companyInfo = CompanyInfo::first();
    $salesOfferDetails = $purchaseOrder->details ? $purchaseOrder->details : '';
    $seller = '';
    $data = [
      'companyInfo' => $companyInfo,
      'document' => $purchaseOrder->toArray(),
      'documentDetails' => $salesOfferDetails,
      'customer' => $supplier->toArray(),
      'customerName' => $supplierName,
      'seller' => $seller,
      'products' => $purchaseOrder['products']
    ];

    $pdf = \PDF::loadView('pdf.purchaseOrder', $data)->setPaper('a4');
    $path = SystemConfiguration::getPublicCompanyPath().'prints/';
    if (!\Storage::exists($path)) {
      \Storage::makeDirectory($path);
    }
    $pdf->save(SystemConfiguration::getPublicCompanyPath(true).'prints/purchaseOrder.pdf');
    return ['url'=> SystemConfiguration::getPublicCompanyPath().'prints/purchaseOrder.pdf'];
  }

  private function filterProductQuantityReceiptOfPurchaseOrders($purchaseOrders)
  {
    $filterPurchaseOrders = [];
    foreach ($purchaseOrders as $key => $purchaseOrder) {
      $purchaseOrdersProducts = [];
      foreach ($purchaseOrder['products'] as $key => $product) {
        if (isset($product['quantityReceipt']) && $product['quantityReceipt'] > 0) {
          array_push($purchaseOrdersProducts, $product);
        }
      }
      if (count($purchaseOrdersProducts) > 0) {
        $purchaseOrder['products'] = $purchaseOrdersProducts;
        array_push($filterPurchaseOrders, $purchaseOrder);
      }
    }
    return $filterPurchaseOrders;
  }

  
  public function getByParameterPost()
  {
    $parameter = Input::all();
    $purchaseOrder = PurchaseOrder::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $purchaseOrder;
  }


  public function generateFromPurchaseQuotation()
  { 
    $dataForGeneratePurchaseOrder = Input::get();
    $newPurchaseOrder = $dataForGeneratePurchaseOrder['purchaseOrder'];
    $newPurchaseOrder['number'] = $this->generatePurchaseOrderNumber();
    $newPurchaseOrder['purchaseQuotationNumber'] = $dataForGeneratePurchaseOrder['prevDocument']['number'];
    $prevStatus = ApprovalDocument::getApprovalsNumber('007');
    $newPurchaseOrder['status'] = ($prevStatus == 0 ? 'Abierto' : 'Pendiente de aprobación');
    $supplier = Supplier::find($newPurchaseOrder['supplier_id']);
    $newPurchaseOrder['supplierIdentification'] = $supplier->identification;
    $newPurchaseOrder['supplier'] = $supplier->toArray();
    if (PurchaseOrder::create($newPurchaseOrder)) {
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      PurchaseQuotationRegisterOrderProducts::fromPurchaseOrder($newPurchaseOrder['number']);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function savePuchaseQuotationFromGenerate($purchaseQuotation, $purchaseOrderNumber)
  {
    $purchaseQuotation['status'] = 'Pedido generado';
    $purchaseQuotation['documentToName'] = 'purchaseOrder';
    $purchaseQuotation['documentToNumber'] = $purchaseOrderNumber;

    $savedPurchaseQuotation = PurchaseQuotation::find($purchaseQuotation['_id']);
    $savedPurchaseQuotation->update($purchaseQuotation);
  }


  public function store()
  {
    $newPurchaseOrder = Input::get();
    $newPurchaseOrder['number'] = $this->generatePurchaseOrderNumber();
    $prevStatus = ApprovalDocument::getApprovalsNumber('007');
    $newPurchaseOrder['status'] = ($prevStatus == 0 ? 'Abierto' : 'Pendiente de aprobación');
    $supplier = Supplier::find($newPurchaseOrder['supplier_id']);
    $newPurchaseOrder['supplierIdentification'] = $supplier->identification;
    $newPurchaseOrder['supplier'] = $supplier->toArray();
    if (PurchaseOrder::create($newPurchaseOrder)) {
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function approval($id)
  {
    $purchaseOrder = PurchaseOrder::find($id);
    $newData = Input::all();
    $approvalStatus = ApprovalDocument::getApprovalStatus('007');
    $newData['status'] = $approvalStatus == '' ? 'Aprobado' : $approvalStatus;
    if($purchaseOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function rejected($id)
  {
    $purchaseOrder = PurchaseOrder::find($id);
    $newData = Input::all();
    $rejectedStatus = ApprovalDocument::getRejectedStatus('007');
    $newData['status'] = $rejectedStatus == '' ? 'Rechazado' : $rejectedStatus;
    if($purchaseOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $purchaseOrder = PurchaseOrder::find($id);
    $newData = Input::all();
    if($purchaseOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function checkPurchaseQuotationStatus($purchaseQuotationNumbers)
  {
    foreach ($purchaseQuotationNumbers as $key => $purchaseQuotationNumber) {
      CheckPurchaseQuotationStatus::purchaseQuotation($purchaseQuotationNumber);
    }
  }

  public function saveConsolidation()
  {
    $newPurchaseOrder = Input::get();
    $newPurchaseOrder['number'] = $this->generatePurchaseOrderNumber();
    $selectedProducts = $newPurchaseOrder['selectedProducts'];
    $supplier = Supplier::find($newPurchaseOrder['supplier_id']);
    $newPurchaseOrder['supplierIdentification'] = $supplier->identification;
    $newPurchaseOrder['supplier'] = $supplier->toArray();
    if(PurchaseOrder::create($newPurchaseOrder)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      PurchaseQuotationRegisterOrderProducts::fromPurchaseOrder($newPurchaseOrder['number'], true);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  public function destroy($id)
  {
    $savedPurchaseOrder = PurchaseOrder::find($id);
    $status = $savedPurchaseOrder->status;
    if ($status == 'Rechazado'){
      $status = 'Rechazado - Anulado';
    } else {
      $status = 'Anulado';
    }
    $fechaAnulacion = new \DateTime();
    $savedPurchaseOrder->annulDate = $fechaAnulacion->format('Y-m-d H:i:s');
    $savedPurchaseOrder->status = $status;
    if ($savedPurchaseOrder->save()) {
      if ($savedPurchaseOrder->status === 'Abierto' || $savedPurchaseOrder->status === 'Pendiente de aprobación') {
        if($savedPurchaseOrder->isConsolidation === true){
          Consolidations::revertFromPurchaseOrder($savedPurchaseOrder->number);
          return ResultMsgMaker::annulSuccess();
        } else {
          RevertOrders::revertFromPurchaseOrder($savedPurchaseOrder->number);
          return ResultMsgMaker::annulSuccess();
        }
      } else {
        $goodsReceipt = GoodsReceipt::warehouse()->where('number', $savedPurchaseOrder->goodsReceiptNumber)->first();
        $goodsReceipt->status = 'Ingreso completo';
        $goodsReceipt->save();
        return ResultMsgMaker::annulSuccess();
      }
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::warehouse()->where('code', '=', '007')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generatePurchaseOrderNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
