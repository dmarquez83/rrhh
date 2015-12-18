<?php namespace App\Http\Controllers;


use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Helpers\SystemConfiguration;
use App\Models\DocumentConfiguration;
use App\Models\SalesOrder;
use App\Models\TemporaryStockCustomerInvoice;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use App\Helpers\GeneralJournalMaker;
use App\Helpers\KardexMaker;
use App\Helpers\MinimunTotalFinalConsumerValidate;
use App\Helpers\CustomerInvoiceQuotasMaker;
use App\Helpers\ElectronicInvoice;
use App\Helpers\RetentionMaker;
use App\Helpers\AccessPassword;
use Illuminate\Support\Facades\Input;
use App\Helpers\ApprovalDocument;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for CustomerInvoice Model
|--------------------------------------------------------------------------
*/

class CustomerInvoiceController extends Controller {

  private $documentConfiguration;

  public function index()
  {
    $customerInvoice = CustomerInvoice::warehouse()->orderBy('number', 'asc')->get();
    return $customerInvoice;
  }

  public function forDashboard()
  {
    $params = Input::all();
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $supplierInvoices = CustomerInvoice::warehouse()
      ->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))])
      ->get(['creationDate', 'totals.total', 'products', 'customer']);
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
    $salesNumberFrom = (isset($params['salesNumberFrom']) ? $params['salesNumberFrom'] : '');
    $salesNumberUntil = (isset($params['salesNumberUntil']) ? $params['salesNumberUntil'] : '');
    $selectedProducts = (isset($params['selectedProducts']) ? $params['selectedProducts'] : []);
    $selectedCustomers = (isset($params['selectedCustomers']) ? $params['selectedCustomers'] : []);
    $haveRetention = (isset($params['haveRetention']) ? $params['haveRetention'] : '');
    $salesCreditNoteNumber = (isset($params['salesCreditNoteNumber']) ? $params['salesCreditNoteNumber'] : '');

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = CustomerInvoice::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $customers = CustomerInvoice::warehouse()
      ->where(function($query) use($startDate, $endDate, $salesNumberFrom, $salesNumberUntil, $selectedProducts, $selectedCustomers, $haveRetention, $salesCreditNoteNumber){
        if ($startDate != '' && $endDate != ''){
          $query->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if ($salesNumberFrom != '' && $salesNumberUntil != ''){
          $query->whereBetween('number', [$salesNumberFrom, $salesNumberUntil]);
        }
        if (count($selectedCustomers) > 0) {
          $query->whereIn('customer.identification', $selectedCustomers);
        }
        if (count($selectedProducts) > 0) {
          $query->whereIn('products.code', $selectedProducts);
        }
        if (is_bool($haveRetention)){
          $query->where('haveRetention', '=', $haveRetention);
        }
        if ($salesCreditNoteNumber === 'null') {
          $query->whereNull('salesCreditNoteNumber');
        }
      })
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

    $totalRecords = CustomerInvoice::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('001');
    $approvalStatus = ApprovalDocument::getApprovalStatus('001');

    $customerInvoices = CustomerInvoice::warehouse()
      ->where(function($query) use($prevStatus, $approvalStatus){
        if($prevStatus != '') {
          $query->where('status', '=', $prevStatus);
        }
        $query->where('status', '!=', $approvalStatus);
      })
      ->whereIn('status', ['Pendiente de Aprobación'])
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $customerInvoices = $customerInvoices->filter(function($customerInvoice) use($searchValue){
        if (stripos($customerInvoice, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $customerInvoices->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $customerInvoices);
    return $returnData;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $customerInvoice = CustomerInvoice::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $customerInvoice;
  }

  public function approval($id)
  {
    $customerInvoice = CustomerInvoice::find($id);
    $newData = Input::all();
    $approvalStatus = ApprovalDocument::getApprovalStatus('001');
    $newData['status'] = ($approvalStatus == '' ? 'Aprobado' : $approvalStatus);
    if($customerInvoice->update($newData)) {
        if (SystemConfiguration::isElectronicDocumentsEnabled()) {
          $customerInvoice->electronicStatus = 'ENVIADO';
          $customerInvoice->save();
        }
        $this->processCustomerInvoice($customerInvoice['_id']);
        foreach ($customerInvoice['products'] as $key => $product) {
          $this->freeReserveStock($product['code'], $product['quantity']);
        }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function rejected($id)
  {
    $customerInvoice = CustomerInvoice::find($id);
    $newData = Input::all();
    $rejectedStatus = ApprovalDocument::getRejectedStatus('001');
    $newData['status'] = $rejectedStatus == '' ? 'Rechazado' : $rejectedStatus;
    if($customerInvoice->update($newData)) {
      foreach ($customerInvoice['products'] as $key => $product) {
        $this->freeReserveStock($product['code'], $product['quantity']);
      }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function store()
  {
    $customerInvoice = Input::get();
    if (MinimunTotalFinalConsumerValidate::validate($customerInvoice)) {
      $customerInvoice['number'] = $this->generateCustomerInvoiceNumber();
      $customerInvoice = CustomerInvoice::create($customerInvoice);
      $customerInvoice->accessPasword = AccessPassword::generate($customerInvoice['number'], 'App\Models\CustomerInvoice');
      $customerInvoice->save();

      if ($customerInvoice) {
        $this->documentConfiguration->secuencial += 1;
        $this->documentConfiguration->save();

        if ($customerInvoice['status'] === 'Abierto') {
          if (SystemConfiguration::isElectronicDocumentsEnabled()) {
            $customerInvoice->electronicStatus = 'ENVIADO';
            $customerInvoice->save();
          }
          $this->processCustomerInvoice($customerInvoice['_id']);
        } else {
          foreach ($customerInvoice['products'] as $key => $product) {
            if ($product['isExpenseItem'] === false) {
              $this->reserveStock($product['code'], $product['quantity']);
            }

          }
        }

        if ($customerInvoice->documentFromModel){
          $documentModel = 'App\\Models\\'.$customerInvoice->documentFromModel;
          $document = $documentModel::where('number', $customerInvoice->documentFromNumber)->first();
          $document->status = 'Factura generada';
          $document->save();
        }

        return ResultMsgMaker::saveSuccessWithExtraData(['number' => $customerInvoice['number'], 'accessPasword' => $customerInvoice['accessPasword']]);

      } else {
        return ResultMsgMaker::error();
      }
    } else {
      return ResultMsgMaker::errorCustom("Factura excede el monto máximo para consumidor final");
    };
  }

  private function processCustomerInvoice($customerInvoiceId)
  {
    $customerInvoice = CustomerInvoice::find($customerInvoiceId);

    if (SystemConfiguration::isElectronicDocumentsEnabled()) {
      $path = base_path().'/artisan';
      $number = $customerInvoice['number'];
      $warehouseId = $customerInvoice['warehouse_id'];
      $cmd = "php $path generateElectronicInvoice '$number' '$warehouseId'";
      $this->execInBackground($cmd);
      //ElectronicInvoice::make($customerInvoice['number']);
    }

    if (SystemConfiguration::isAccountingEnabled()) {
      GeneralJournalMaker::generateEntry($customerInvoice->toArray(), '001');
    }

    if (isset($customerInvoice['quotas'])) {
      CustomerInvoiceQuotasMaker::generate($customerInvoice);
    }
    $this->registerProductsInKardex($customerInvoice['products'], $customerInvoice['number']);
    $this->updateCustomer($customerInvoice['customer']);

    if (isset($customerInvoice['salesOrderNumber'])) {
      $this->updateSalesOrder($customerInvoice);
    }
    $customerInvoice->status = 'Facturado';
    $customerInvoice->save();
    RetentionMaker::generateFromSale($customerInvoice['_id']);
  }

  private function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
      pclose(popen("start /B ". $cmd, "r"));
    }
    else {
      exec($cmd . " > /dev/null &");
    }
  }

  private function reserveStock($productCode, $quantity)
  {
    $product = Product::warehouse()->where('code', '=', $productCode)->first();
    $product->reserveStock += $quantity;
    $product->save();
  }

  private function freeReserveStock($productCode, $quantity)
  {
    $product = Product::warehouse()->where('code', '=', $productCode)->first();
    $product->reserveStock -= $quantity;
    $product->save();
  }

  public function registerProductsInKardex($products, $customerInvoiceNumber)
  {
    foreach($products as $product) {
      if ($product['isExpenseItem'] === false) {
        KardexMaker::registerOutput($product, $customerInvoiceNumber, 'CustomerInvoice');
      }
    }
  }

  public function updateCustomer($customer)
  {
    unset($customer['_id']);
    $saveCustomer = Customer::where('identification', '=', $customer['identification']);
    if($saveCustomer){
      $saveCustomer->update($customer);
    } else {
      Customer::firstOrCreate($customer);
    }
  }

  private function updateSalesOrder($customerInvoice)
  {
    $salesOrder = SalesOrder::warehouse()->where('number', '=', $customerInvoice['salesOrderNumber'])->first();
    $temporaySalesHistory = $this->updateSalesHistory($salesOrder->temporaryStockCustomerInvoiceNumberHistory);
    $this->calculateReserveStockProduct($salesOrder, $customerInvoice);
    $salesOrder->products = $this->registerInvoiceProduct($salesOrder, $customerInvoice);
    $salesOrder->status = $this->checkSalesOrderStatus($salesOrder);
    $salesOrder->temporaryStockCustomerInvoiceNumberHistory = $temporaySalesHistory;
    $salesOrder->save();
  }

  private function updateSalesHistory($temporaySalesHistory)
  {
    if (count($temporaySalesHistory) > 0) {
      foreach ($temporaySalesHistory as $key => $history) {
        if ($history['invoincing'] === false ) {
          $temporaryStockCustomerInvoice = TemporaryStockCustomerInvoice::warehouse()
            ->where('number', '=', $history['number'])->first();
          $temporaryStockCustomerInvoice->status = 'Facturado';
          $temporaryStockCustomerInvoice->save();
          $temporaySalesHistory[$key]['invoincing'] = true;
        }
      }
    }

  }

  private function registerInvoiceProduct($salesOrder, $customerInvoice)
  {
    $salesOrderProducts = $salesOrder['products'];
    foreach($customerInvoice['products'] as $product){
      $findProductKey = array_search($product['code'], array_column($salesOrderProducts, 'code'));
      if (!isset($salesOrderProducts[$findProductKey]['invoicingQuantity'])) {
        $salesOrderProducts[$findProductKey]['invoicingQuantity'] = 0; 
      }
      $salesOrderProducts[$findProductKey]['invoicingQuantity'] += $product['quantity'];
    }
    return $salesOrderProducts;
  }

  private function calculateReserveStockProduct($salesOrder, $customerInvoice)
  {
    if ($salesOrder['status'] === 'Venta parcial' || $salesOrder['status'] === 'Venta completa' || $salesOrder['status'] === 'Recibido parcial' 
      || $salesOrder['status'] === 'Recibido completo') {
      foreach($customerInvoice['products'] as $product){
        $foundProduct = Product::warehouse()->where('code', '=', $product['code'])->first();
        $foundProduct->reserveStock -= $product['quantity'];
        $foundProduct->save();
      }
    }
  }

  private function checkSalesOrderStatus($salesOrder)
  {
    $completeInvoiceProduct = 0;
    $partialInvoiceProduct = 0;
    $nullInvoiceProduct = 0;
    $totalQuantityProduct = count($salesOrder['products']);
    foreach($salesOrder['products'] as $product){
      $invoicingQuantity = isset($product['invoicingQuantity']) ? $product['invoicingQuantity'] : 0;

      if (round($invoicingQuantity, 2) === round($product['quantity'], 2)) {
        $completeInvoiceProduct += 1;
      }
      if (round($invoicingQuantity, 2) < round($product['quantity'], 2) && round($invoicingQuantity, 2) > 0.00) {
        $partialInvoiceProduct += 1;
      }
      if (round($invoicingQuantity, 2) === 0.00) {
        $nullInvoiceProduct += 1;
      }
    }

    if ($partialInvoiceProduct > 0) {
      return 'Facturado parcial';
    } else if ($completeInvoiceProduct === $totalQuantityProduct){
      return 'Facturado';
    } else if ($completeInvoiceProduct > 0 && $completeInvoiceProduct < $totalQuantityProduct) {
      return 'Facturado parcial';
    }
    return $salesOrder['status'];
  }

  public function specificData()
  {
    $colums = Input::all();
    $customerInvoice = CustomerInvoice::warehouse()->orderBy('number', 'asc')->get($colums);

    return $customerInvoice;
  }

  private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '001')
      ->where('warehouseId', '=', Session::get('warehouseId'))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateCustomerInvoiceNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

  public function downloadFile(){
    $customerInvoiceData = Input::all();

    $customerInvoice = CustomerInvoice::where('number', '=', $customerInvoiceData['number'])->first();
    $fechaDocumento = new \DateTime($customerInvoice->creationDate);
    $fechaFormateada = $fechaDocumento->format('dmY_His');

    if ($customerInvoiceData['type'] == 'pdf') {
      $electronicPath = SystemConfiguration::getElectronicDocumentsPath();
      $filePath = $electronicPath.'factura_'.$fechaFormateada.'_ride.pdf';
      $fileNameDownload = SystemConfiguration::getPublicCompanyPath().'download_file.pdf';
      if(\Storage::exists($fileNameDownload)){
        \Storage::delete($fileNameDownload);
      }
      \Storage::copy($filePath, $fileNameDownload);
      return str_replace("public/", "", $fileNameDownload);
    } else if ($customerInvoiceData['type'] == 'xml') {
      $electronicPath = SystemConfiguration::getElectronicDocumentsPath(true);
      $filePath = $electronicPath.'factura_'.$fechaFormateada.'_autorizado.xml';
      header('Content-Description: File Transfer');
      header('Content-Type: application/xml');
      header('Content-Disposition: attachment; filename='.basename($filePath));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filePath));
      ob_clean();
      flush();
      readfile($filePath);
    }
  }

  public function resendElectronicDocument()
  {
    $electronicDocument = Input::all();

    if (SystemConfiguration::isElectronicDocumentsEnabled()) {
      $path = base_path().'/artisan';
      $number = $electronicDocument['documentNumber'];
      $warehouseId = $electronicDocument['warehouse_id'];
      $cmd = "php $path generateElectronicInvoice '$number' '$warehouseId'";
      $this->execInBackground($cmd);
      return ResultMsgMaker::successCustom('El documento ha sido reenviado');
    }
    return ResultMsgMaker::erroCustom('No esta activado facturación electrónica');
  }

}
