<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\SystemConfiguration;
use App\Models\DocumentConfiguration;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\HistoryStockProductReserve;
use App\Models\PurchaseQuotation;
use App\Models\SalesOrder;
use App\Models\TemporaryStockCustomerInvoice;
use App\Models\PurchaseOrder;
use App\Models\SupplierPay;
use App\Helpers\ResultMsgMaker;
use App\Helpers\KardexMaker;
use App\Helpers\TemporaryKardexMaker;
use App\Helpers\GeneralJournalMaker;
use App\Helpers\RetentionMaker;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Models\TemporaryKardex;
use App\Models\PurchaseRetention;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SupplierInvoice Model
|--------------------------------------------------------------------------
*/

class SupplierInvoiceController extends Controller {

  private $documentConfiguration;

  public function index()
  {
    $purchaseQuotations = SupplierInvoice::warehouse()->get();

    return $purchaseQuotations;
  }

  public function forDashboard()
  {
    $params = Input::all();
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $supplierInvoices = SupplierInvoice::warehouse()
      ->whereBetween('documentDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))])
      ->get(['documentDate', 'totals.total']);
    return $supplierInvoices;
  }

  public function forTable()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $supplierInvoiceNumberFrom = (isset($params['supplierInvoiceNumberFrom']) ? $params['supplierInvoiceNumberFrom'] : '');
    $supplierInvoiceNumberUntil = (isset($params['supplierInvoiceNumberUntil']) ? $params['supplierInvoiceNumberUntil'] : '');
    $selectedProducts = (isset($params['selectedProducts']) ? $params['selectedProducts'] : []);
    $selectedSuppliers = (isset($params['selectedSuppliers']) ? $params['selectedSuppliers'] : []);

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = SupplierInvoice::warehouse()->count();

    $supplierInvoices = SupplierInvoice::warehouse()
      ->where(function($query) use($startDate, $endDate, $supplierInvoiceNumberFrom, $supplierInvoiceNumberUntil, $selectedProducts, $selectedSuppliers){
        if($startDate != '' && $endDate != ''){
          $query->whereBetween('documentDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if($supplierInvoiceNumberFrom != '' && $supplierInvoiceNumberUntil != ''){
          $query->whereBetween('number', [$supplierInvoiceNumberFrom, $supplierInvoiceNumberUntil]);
        }
        if(count($selectedSuppliers) > 0) {
          $query->whereIn('supplier.identification', $selectedSuppliers);
        }
        if(count($selectedProducts) > 0) {
          $query->whereIn('products.code', $selectedProducts);
        }
      })
      ->where(function($query) use($searchValue){
        if ($searchValue != '') {
          $query->orWhere('number', 'like', '%'.$searchValue.'%');
          $query->orWhere('supplier.name', 'like', '%'.$searchValue.'%');
          $query->orWhere('supplier.surname', 'like', '%'.$searchValue.'%');
          $query->orWhere('supplier.comercialName', 'like', '%'.$searchValue.'%');
          $query->orWhere('total', 'like', '%'.$searchValue.'%');
          $query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
          $query->orWhere('documentDate', 'like', '%'.$searchValue.'%');
        }
      })
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $recordsFiltered = $supplierInvoices->count();


    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $supplierInvoices);
    return $returnData;
  }

  public function specificData()
  {
    $colums = Input::all();
    $supplierInvoices = SupplierInvoice::orderBy('number', 'asc')->get($colums);

    return $supplierInvoices;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $supplierInvoice = SupplierInvoice::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $supplierInvoice;
  }


  public function store()
  {
    $supplierInvoice = Input::get();
    if ($this->validateDistributionQuantityOfGoodsReceipt($supplierInvoice)) {
      $supplierInvoice['internalNumber'] = $this->generatePurchaseNumber();
      $supplierInvoice['supplier_id'] = isset($supplierInvoice['supplier']['_id']) ? $supplierInvoice['supplier']['_id'] : '';

      $xmlFile = isset($supplierInvoice['xmlFile']) ? $supplierInvoice['xmlFile'] : null;
      $pdfFile = isset($supplierInvoice['pdfFile']) ? $supplierInvoice['pdfFile'] : null;

      unset($supplierInvoice['xmlFile']);
      unset($supplierInvoice['pdfFile']);
      $supplierInvoice = SupplierInvoice::create($supplierInvoice);

      if ($supplierInvoice) {
        $this->updateSecuencial();

        if (isset($supplierInvoice['isElectronicDocument']) && $supplierInvoice['isElectronicDocument'] === true){
          $this->saveElectronicDocuments($xmlFile, $pdfFile, $supplierInvoice);
        }

        if(isset($supplierInvoice['pays'])) {
          $this->generateSupplierPays($supplierInvoice['pays'], $supplierInvoice);
        }

        foreach($supplierInvoice['products'] as $product) {
          if ($product['isExpenseItem'] === false) {
            KardexMaker::registerInput($product, $supplierInvoice['number'], 'SupplierInvoice');
          }
        }

        $supplierData = $supplierInvoice['supplier'];
        unset($supplierData['_id']);

        if($supplierInvoice['supplier_id'] !== ''){
          Supplier::where('identification', '=', $supplierData['identification'])
            ->update($supplierData);
        } else {
          Supplier::create($supplierInvoice['supplier']);
        }


        if (isset($supplierInvoice['purchaseOrderNumber'])){
          $this->checkReserveStock($supplierInvoice);
          $this->registerOuputTemporaryStock($supplierInvoice);
          $purchaseOrder = PurchaseOrder::warehouse()->where('number', '=', $supplierInvoice['purchaseOrderNumber'])->first();
          $purchaseOrder->status = 'Factura Ingresada';
          $purchaseOrder = $this->registerInvoiceProduct($purchaseOrder, $supplierInvoice);
          $purchaseOrder->save();
        }

        if (SystemConfiguration::isAccountingEnabled()){
          GeneralJournalMaker::generateEntry($supplierInvoice->toArray(), '012');
        }

        RetentionMaker::generateFromPurchase($supplierInvoice['_id']);
        
        return ResultMsgMaker::saveSuccess();
      } else {

        return ResultMsgMaker::error();
      }
    } else {
      return ResultMsgMaker::errorCustom("No se puede ingresar una factura sin antes haber distribuido la totalidad del ingreso de mercadería");
    }  
  }

  private function validateDistributionQuantityOfGoodsReceipt($supplierInvoice) 
  {
    if (isset($supplierInvoice['purchaseOrderNumber']) && $supplierInvoice['purchaseOrderNumber'] !== '') {

      $purchaseOrder = PurchaseOrder::warehouse()->where('number', $supplierInvoice['purchaseOrderNumber'])->first();
      if (isset($purchaseOrder['purchaseQuotationNumber']) || isset($purchaseOrder['purchaseQuotationNumbers'])) {
        $purchaseQuotationsNumber = isset($urchaseOrder['purchaseQuotationNumber']) ? $purchaseOrder['purchaseQuotationNumber'] : '';
        $purchaseQuotationsNumbers = isset($urchaseOrder['purchaseQuotationNumbers']) ? $purchaseOrder['purchaseQuotationNumbers'] : [];
        $purchaseQuotations = [];
        if ($purchaseQuotationsNumber !== ''){
          $purchaseQuotations = PurchaseQuotation::warehouse()->where('number', $purchaseQuotationsNumber)->get();
        } else if (count($purchaseQuotationsNumbers) > 0) {
          $purchaseQuotations = PurchaseQuotation::warehouse()->whereIn('number', $purchaseQuotationsNumbers)->get();
        }

        if (count($purchaseQuotations) > 0) {
          foreach ($purchaseQuotations as $key => $purchaseQuotation) {
            if (isset($purchaseQuotation['documentFromNumber'])) {
              if ($purchaseOrder['distributionComplete'] === false) {
                return false;
              }
            }
          }
          return true;
        } else {
          return true;
        }
        
      }
      return true;
    }
    return true;
  }

  private function registerOuputTemporaryStock($supplierInvoice)
  {
    foreach ($supplierInvoice['products'] as $key => $product) {
      $concept = 'Registro salida de stock temporal según Factura de Proveedor n '.$supplierInvoice['number'];
      TemporaryKardexMaker::registerOutput($supplierInvoice['number'], $product['code'], $product['_id'], $product['quantity'], 'SupplierInvoice', $concept);
    }
  }


  private function generateSupplierPays($pays, $supplierInvoice)
  {
    if(count($pays) > 0){
      foreach ($pays as $key => $pay) {
        $newPay = $pay;
        $newPay['supplierInvoiceNumber'] = $supplierInvoice['number'];
        $newPay['supplier_id'] = $supplierInvoice['supplier']['_id'];
        $newPay['status'] = 'Pendiente';
        $newPay['paid'] = 0;
        $newPay['pendingPayment'] = $pay['total'];
        $newPay['totalInvoice'] = $supplierInvoice['totals']['total'];
        SupplierPay::create($newPay);
      }
    }
  }

  private function checkReserveStock($supplierInvoice)
  {
    $historyReserves = $this->getHisoryReservesByPurchaseOrderNumber($supplierInvoice['purchaseOrderNumber']);
    if (count($historyReserves) > 0) {
      $supplierInvoiceProducts = $supplierInvoice['products'];

      $concept = "Para registrar el reingreso de las reservas según Factura de Proveedor n ".$supplierInvoice['number'];
      $historyIds = [];
      foreach ($supplierInvoiceProducts as $key => $product) {
        $totalReserve = 0;
        foreach ($historyReserves as $key => $history) {
          if ($history['productCode'] == $product['code']) {
            $totalReserve += $history['quantity'];
            array_push($historyIds, $history['_id']);
          }
        }
        TemporaryKardexMaker::registerCleanInput($supplierInvoice['number'], $product, $totalReserve, 'SupplierInvoice', $concept);
      }
      $this->updateHistoryReservesRegister($historyIds);
    }
  }

  private function getHisoryReservesByPurchaseOrderNumber($purchaseOrderNumber)
  {
    $purchaseOrder = PurchaseOrder::warehouse()->where('number', '=', $purchaseOrderNumber)->first();
    $purchaseQuotationsNumbers = $this->getPurchaseQuotationsNumbersFromPurchaseOrder($purchaseOrder->toArray());
    if (count($purchaseQuotationsNumbers) > 0) {
      $historyReserves = [];
      $historyReserves = HistoryStockProductReserve::warehouse()->whereIn('purchaseQuotationNumber', $purchaseQuotationsNumbers)
        ->where('invoicing', '=', false)->get();
      return $historyReserves->toArray();
    }
    return [];
  }

  private function getPurchaseQuotationsNumbersFromPurchaseOrder($purchaseOrder)
  {
    
    $purchaseQuotations = [];
    if (isset($purchaseOrder['purchaseQuotationNumbers']) ) {
      $purchaseQuotations = PurchaseQuotation::warehouse()->whereIn('number', $purchaseOrder['purchaseQuotationNumbers'])
        ->select('number')
        ->whereNotNull('documentFromNumber')
        ->get();
    }
    if (isset($purchaseOrder['documentFromNumber']) ) {
      $purchaseQuotations = PurchaseQuotation::warehouse()->where('number', '=', $purchaseOrder['purchaseQuotationNumber'])
      ->select('number')
      ->get();
    }

    if (count($purchaseQuotations) > 0) {
      $purchaseQuotationsNumbers = array_pluck($purchaseQuotations->toArray(), 'number');
      return $purchaseQuotationsNumbers;
    }

    return [];
  }

  private function updateHistoryReservesRegister($historyIds)
  {
    foreach ($historyIds as $key => $historyId) {
      $history = HistoryStockProductReserve::find($historyId);
      $history->invoicing = true;
      $history->save();
    }
  }

  private function registerInvoiceProduct($purchaseOrder, $supplierInvoice)
  {
    $purchaseOrderProducts = $purchaseOrder->products;
    foreach ($supplierInvoice['products'] as $key => $product) {
      $result = $this->findProduct($product['code'], $purchaseOrderProducts);
      $findProduct = $result['product'];
      $findProductKey = $result['key'];
      $findProduct = $this->calculateInvoiceProduct($findProduct, $product['quantity']);
      $findProduct = $this->generateHistoryInvoiceProduct($findProduct, $supplierInvoice, $product['quantity']);
      $purchaseOrderProducts[$findProductKey] = $findProduct;
    }
    $purchaseOrder->products = $purchaseOrderProducts;
    return $purchaseOrder;
  }

  private function findProduct($productCode, $productsArray)
  {
    $keyOfProduct = array_search($productCode, array_column($productsArray, 'code'));
    $findProduct = $productsArray[$keyOfProduct];
    return ['product'=> $findProduct, 'key' => $keyOfProduct];
  }

  private function calculateInvoiceProduct($product, $quantity)
  {
    if(!isset($product['invoiceQuantity'])) {
      $product['invoiceQuantity'] = 0;
    }

    $product['invoiceQuantity'] += $quantity;
    return $product;
  }

  private function generateHistoryInvoiceProduct($product, $supplierInvoice, $quantity)
  {
    $newInvoiceProductHistory = [
      'supplierInvoiceNumber'=> $supplierInvoice['number'],
      'date' => $supplierInvoice['creationDate'],
      'invoiceQuantity' => $quantity
    ];

    if(isset($product['invoiceHistory'])){
      array_push($product['invoiceHistory'], $newInvoiceProductHistory);
    } else {
      $product['invoiceHistory'] = [$newInvoiceProductHistory];
    }
    return $product;
  }


  private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '012')
      ->where('warehouseId', '=', Session::get('warehouseId'))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function updateSecuencial()
  {
    $this->documentConfiguration->secuencial += 1;
    $this->documentConfiguration->save();
  }

  private function generatePurchaseNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

  public function getColumnsByParameters()
  {
    $parameters = Input::get('parameters');
    $columns = Input::get('columns');
    $supplierInvoices = SupplierInvoice::warehouse()
      ->orderBy('name', 'desc')
      ->where(function($query) use($parameters){
        foreach($parameters as $key => $parameter){
          $query->where($key, '=', $parameter);
        }
      })
      ->select($columns)->get();
    return $supplierInvoices;
  }

  private function saveElectronicDocuments($xmlFile, $pdfFile, $supplierInvoice)
  {
    $fechaDocumento = new \DateTime($supplierInvoice->creationDate);
    $fechaFormateada = $fechaDocumento->format('dmY_His');
    $fileName = '/factura_'.$fechaFormateada;
    $companyName = Session::get('companyInformation')['businessName'];
    $companyName = strtolower($this->cleanString(trim($companyName))).Session::get('companyInformation')['identification'];
    $path = 'storage/'.$companyName.'/purchases'.$fileName;

    \Storage::makeDirectory('storage/'.$companyName.'/purchases', true, true);

    if ($pdfFile!== null) {
      $finalPdfFile = base64_decode(str_replace('data:application/pdf;base64,','',$pdfFile));
      \Storage::put($path.'.pdf', $finalPdfFile);
    }
    $finalXmlFile = base64_decode(str_replace('data:text/xml;base64,','',$xmlFile));
    \Storage::put($path.'.xml', $finalXmlFile);
  }

  private function cleanString($texto)
  {
    $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);
    return $textoLimpio;
  }

  public function downloadFile()
  {
    $supplierInvoiceData = Input::all();
    $supplierInvoice = SupplierInvoice::where('number', '=', $supplierInvoiceData['number'])->first();

    $fechaDocumento = new \DateTime($supplierInvoice->creationDate);
    $fechaFormateada = $fechaDocumento->format('dmY_His');
    $fileName = '/factura_'.$fechaFormateada;
    $companyName = Session::get('companyInformation')['businessName'];
    $companyName = strtolower($this->cleanString(trim($companyName))).Session::get('companyInformation')['identification'];
    $path = 'storage/'.$companyName.'/purchases'.$fileName;

    if ($supplierInvoiceData['type'] == 'pdf') {
      $fileNameDownload = 'public/'.$companyName.'_downloadedInvoice.pdf';

      if(\Storage::exists($fileNameDownload)){
        \Storage::delete($fileNameDownload);
      }
      \Storage::copy($path.".pdf", $fileNameDownload);

      return $companyName.'_downloadedInvoice.pdf';

    } else if ($supplierInvoiceData['type'] == 'xml') {
      $fileNameDownload = 'public/'.$companyName.'_downloadedInvoice.xml';

      if(\Storage::exists($fileNameDownload)){
        \Storage::delete($fileNameDownload);
      }
      \Storage::copy($path.".xml", $fileNameDownload);

      return $companyName.'_downloadedInvoice.xml';
    }
  }

  public function annul()
  {
    $supplierInvoice = Input::all();
    $savedSupplierInvoice = SupplierInvoice::where('number', '=', $supplierInvoice['number'])->first();
    $savedSupplierInvoice->status = 'Anulado';
    $savedSupplierInvoice->save();

    if (SystemConfiguration::isAccountingEnabled()) {
      GeneralJournalMaker::generateAnnulEntry($savedSupplierInvoice->generalJournalEntryNumber);
    }

    return ResultMsgMaker::saveSuccess();
  }

  public function update($id)
  {
    $newData = Input::all();
    $supplierInvoice = SupplierInvoice::find($id);
    $prevSupplierInvoiceNumber = $supplierInvoice->number;
    if($supplierInvoice->update($newData)){
      $this->updateDependDocuments($newData, $prevSupplierInvoiceNumber);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateDependDocuments($supplierInvoice, $prevSupplierInvoiceNumber)
  {
    $purchaseRetention = PurchaseRetention::where('supplierInvoiceNumber', $prevSupplierInvoiceNumber)->first();
    if ($purchaseRetention) {
      $purchaseRetention->supplierInvoiceNumber = $supplierInvoice['number'];
      $purchaseRetention->supplierInvoice = $supplierInvoice;
      $purchaseRetention->save();
    }

  }


}
