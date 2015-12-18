<?php namespace App\Http\Controllers;

use App\Helpers\KardexMaker;
use App\Models\Customer;
use App\Models\DocumentConfiguration;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\ImportOrder;
use App\Helpers\TemporaryKardexMaker;
use App\Helpers\PurchaseOrder\Mailing;
use App\Helpers\ResultMsgMaker;
use App\Helpers\CheckGoodsReceiptStatus;
use App\Helpers\PurchaseOrder\HistoryProductReceipt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for GoodsReceipt Model
|--------------------------------------------------------------------------
*/

class GoodsReceiptController extends Controller {

  public function index()
  {
    $purchaseQuotations = GoodsReceipt::warehouse()->get();

    return $purchaseQuotations;
  }

  public function forTable(){
  
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = GoodsReceipt::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = GoodsReceipt::warehouse()
      ->with('supplier')
      ->skip($start)
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

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $goodsReceipt = GoodsReceipt::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $goodsReceipt;
  }

  private function compaqOrder($purchaseOrder)
  {
    $goodsReceipt = [];
    $fechaCreacion = new \DateTime();
    $goodsReceipt['sendEmailToSupplier'] = $purchaseOrder['sendEmailToSupplier'];
    $goodsReceipt['number'] = $this->generateGoodsReceiptNumber();
    $goodsReceipt['purchaseOrderNumber'] = $purchaseOrder['number'];
    $goodsReceipt['documentName'] = 'Pedido de proveedor';
    $goodsReceipt['creationDate'] = $fechaCreacion->format('Y-m-d h:i:s');
    $goodsReceipt['deliveryDate'] = $purchaseOrder['deliveryDate'];
    $goodsReceipt['supplier_id'] = $purchaseOrder['supplier_id'];
    $goodsReceipt['status'] = 'Pendiente de entrega';
    $goodsReceipt['concept'] = 'Pedido a proveedor';
    $goodsReceipt['products'] = [];
    foreach ($purchaseOrder['products'] as $key => $product) {
      $newProduct = [];
      $newProduct['_id'] = $product['_id'];
      $newProduct['code'] = $product['code'];
      $newProduct['name'] = $product['name'];
      $newProduct['unitOfMeasurement'] = $product['unitOfMeasurement'];
      $newProduct['billing_taxes'] = $product['billing_taxes'];
      $newProduct['supplier_id'] = isset($product['supplier_id']) ? $product['supplier_id'] : null;
      $newProduct['description'] = $product['description'];
      $newProduct['quantity'] = $product['quantity'];
      $newProduct['quantityReceipt'] = 0;
      $newProduct['quantityRemaining'] = 0;
      if (isset($product['purchaseQuotationNumber'])){
        $newProduct['purchaseQuotationNumber'] = $product['purchaseQuotationNumber'];
      }
      array_push($goodsReceipt['products'], $newProduct);
    }
    return $goodsReceipt;
  }

  private function compaqSalesCreditNote($salesCreditNote)
  {
    $goodsReceipt = [];
    $fechaCreacion = new \DateTime();
    $goodsReceipt['number'] = $this->generateGoodsReceiptNumber();
    $goodsReceipt['salesCreditNoteNumber'] = $salesCreditNote['number'];
    $goodsReceipt['documentName'] = 'Nota de Credito cliente';
    $goodsReceipt['creationDate'] = $fechaCreacion->format('Y-m-d h:i:s');
    $goodsReceipt['deliveryDate'] = $fechaCreacion->format('Y-m-d h:i:s');
    $goodsReceipt['customer'] = Customer::where('identification', '=', $salesCreditNote['customerIdentification'])->first()->toArray();
    $goodsReceipt['status'] = 'Pendiente de revisión';
    $goodsReceipt['concept'] = 'Devolución factura cliente';
    $goodsReceipt['products'] = [];
    foreach ($salesCreditNote['products'] as $key => $product) {
      $newProduct = [];
      $newProduct['_id'] = $product['_id'];
      $newProduct['code'] = $product['code'];
      $newProduct['name'] = $product['name'];
      $newProduct['unitOfMeasurement'] = $product['unitOfMeasurement'];
      $newProduct['billing_taxes'] = $product['billing_taxes'];
      $newProduct['supplier_id'] = isset($product['supplier_id']) ? $product['supplier_id'] : null;
      $newProduct['description'] = $product['description'];
      $newProduct['price'] = $product['price'];
      $newProduct['total'] = $product['total'];
      $newProduct['quantity'] = $product['quantity'];
      $newProduct['quantityReceipt'] = $product['quantity'];
      $newProduct['quantityRemaining'] = 0;
      $newProduct['quantityDamage'] = 0;
      array_push($goodsReceipt['products'], $newProduct);
    }
    return $goodsReceipt;
  }

  public function storeFromPurchaseOrder()
  {
    $purchaseOrder = Input::all();
    $finalGoodsReceipt = $this->compaqOrder($purchaseOrder);
    if(GoodsReceipt::create($finalGoodsReceipt)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->updatePurchaseOrderStatus($purchaseOrder['number'], $finalGoodsReceipt['number']);
      if ($purchaseOrder['sendEmailToSupplier'] === true) {
        Mailing::sendMail($purchaseOrder['number']);
      }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function storeFromSalesCreditNote($salesCreditNote)
  {
    $newGoodsReceipt = $this->compaqSalesCreditNote($salesCreditNote);
    if(GoodsReceipt::create($newGoodsReceipt)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  private function updatePurchaseOrderStatus($orderNumber, $goodsReceiptNumber)
  {
    $order = PurchaseOrder::warehouse()->where('number', '=', $orderNumber)->first();
    $order['status'] = 'Pedido enviado';
    $order['goodsReceiptNumber'] = $goodsReceiptNumber;
    $order->save();
  }

  public function unsubscribe()
  {
    $newData = Input::all();
    $goodsReceipt = GoodsReceipt::find($newData['_id']);
    if($goodsReceipt->update($newData)) {
      CheckGoodsReceiptStatus::goodsReceipt($newData['number']);
      $this->checkProductsUnsubscribe($newData);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  private function checkProductsUnsubscribe($goodsReceipt)
  {
    $goodsReceiptProducts = $goodsReceipt['products'];
    foreach ($goodsReceiptProducts as $product) {
      $concept = 'Por devolución de mercadería segun nota de credito n/'.$goodsReceipt['number'];
      KardexMaker::registerCleanInput($product, $product['quantityReceipt'], $goodsReceipt['number'], $concept, 'GoodsReceipt');
      if (isset($product['quantityForUnsubscribe']) > 0) {
        $concept = 'Para dar de baja mercadería segun nota de credito n/'.$goodsReceipt['number'];
        KardexMaker::registerCleanOutput($product, $product['quantityForUnsubscribe'], $goodsReceipt['number'], $concept, 'GoodsReceipt');
      }

    }
  }


  public function store()
  {
    $newGoodsReceipt = Input::get();
    $newGoodsReceipt['number'] = $this->generateGoodsReceiptNumber();
    if(GoodsReceipt::create($newGoodsReceipt)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();

      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $goodsReceipt = GoodsReceipt::find($id);
    $inputGoodsReceipt = Input::all();
    $newData = $inputGoodsReceipt;
    $newData = $this->calculateProductReceipt($newData);
    if ($goodsReceipt->update($newData)) {
      HistoryProductReceipt::registerFromGoodsReceipt($inputGoodsReceipt);
      CheckGoodsReceiptStatus::goodsReceipt($newData['number']);
      TemporaryKardexMaker::registerInputFromGoodsReceipt($inputGoodsReceipt);
      $this->changePurchaseOrderStatus($id);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function changePurchaseOrderStatus($goodsReceiptId)
  {
    $savedGoodsReceipt = GoodsReceipt::find($goodsReceiptId);
    $status = $savedGoodsReceipt->status;
    $purchaseOrder = PurchaseOrder::warehouse()->where('number', '=', $savedGoodsReceipt->purchaseOrderNumber)->first();
    $products = [];
    foreach ($purchaseOrder['products'] as $keyProductPuchaseOrder => $productPurchaseOrder) {
      $newProduct = $productPurchaseOrder;
      foreach ($savedGoodsReceipt['products'] as $keyProductGoodsReceipt => $productGoodsReceipt) {
        if ($newProduct['code'] == $productGoodsReceipt['code']) {
          $newProduct['quantityReceipt'] = $productGoodsReceipt['quantityReceipt'];
        }
      }
      array_push($products, $newProduct);
    }
    $purchaseOrder->products = $products;
    if ($status == 'Ingreso parcial') {
      $purchaseOrder['status'] = 'Recibido parcial';
      $purchaseOrder->save();
    }
    if ($status == 'Ingreso completo') {
      $purchaseOrder['status'] = 'Recibido completo';
      $purchaseOrder->save();
    }
  }

  private function calculateProductReceipt($goodsReceipt)
  {
    foreach ($goodsReceipt['products'] as $key => $product) {
      $goodsReceipt['products'][$key]['quantityReceipt'] += $product['quantityRemaining'];
      $goodsReceipt['products'][$key]['quantityRemaining'] = 0;
    }
    return $goodsReceipt;
  }

  public function destroy($id)
  {
    if(GoodsReceipt::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '010')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateGoodsReceiptNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
