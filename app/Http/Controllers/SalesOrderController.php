<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use App\Models\DocumentConfiguration;
use App\Models\SalesOrder;
use App\Models\SalesOffer;
use App\Helpers\ApprovalDocument;
use App\Helpers\SystemConfiguration;
use App\Models\Customer;
use App\Models\CompanyInfo;
use App\Helpers\SalesOrder\AnnulProcess;


/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SalesOrder Model
|--------------------------------------------------------------------------
*/

class SalesOrderController extends Controller {

 public function index()
  {
    $SalesOrders = SalesOrder::warehouse()->with('customer')->get();

    return $SalesOrders;
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
    $salesNumberFrom = (isset($params['salesNumberFrom']) ? $params['salesNumberFrom'] : '');
    $salesNumberUntil = (isset($params['salesNumberUntil']) ? $params['salesNumberUntil'] : '');
    $selectedProducts = (isset($params['selectedProducts']) ? $params['selectedProducts'] : []);
    $selectedCustomers = (isset($params['selectedCustomers']) ? $params['selectedCustomers'] : []);
    $status = (isset($params['status']) ? $params['status'] : []);

    $searchValue = $params['search']['value'];

    $salesOrderQuery = SalesOrder::warehouse()
      ->where(function($query) use($startDate, $endDate, $status, $salesNumberFrom, $salesNumberUntil, $selectedProducts, $selectedCustomers){
        if(count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if($startDate != ''&& $endDate != '') {
          $query->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if ($salesNumberFrom != '' && $salesNumberUntil != ''){
          $query->whereBetween('number', [$salesNumberFrom, $salesNumberUntil]);
        }
        if (count($selectedCustomers) > 0) {
          $query->whereIn('customerIdentification', $selectedCustomers);
        }
        if (count($selectedProducts) > 0) {
          $query->whereIn('products.code', $selectedProducts);
        }
      });

    $totalRecords = $salesOrderQuery->count(); 

    if ($searchValue !== '') {
      $salesOrderQuery->where(function($query) use($searchValue){
          if ($searchValue != '') {
            $query->orWhere('customer.names', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.surnames', 'like', '%'.$searchValue.'%');
            $query->orWhere('customerIdentification', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.comercialName', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.businessName', 'like', '%'.$searchValue.'%');
            $query->orWhere('status', 'like', '%'.$searchValue.'%');
            $query->orWhere('number', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.code', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.name', 'like', '%'.$searchValue.'%');
            $query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.total', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.iva', 'like', '%'.$searchValue.'%');
          }
      });
    }  

    $salesOrders = $salesOrderQuery
      ->with('customer')
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $recordsFiltered = $salesOrders->count();  

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesOrders
    ];

    return $returnData;
  }


  public function getByParameterPost()
  {
    $parameter = Input::all();
    $salesOrder = SalesOrder::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $salesOrder;
  }

  public function forTemporaryStockCustomerInvoiceForTable()
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
    $customer_id = (isset($params['customer_id']) ? $params['customer_id'] : '');

    $searchValue = $params['search']['value'];

    $salesOrderQuery = SalesOrder::warehouse()
      ->where('customer_id', '=', $customer_id)
      ->whereIn('status', ['Recibido completo', 'Recibido parcial', 'Venta parcial']);

    $totalRecords = $salesOrderQuery->count(); 

    if ($searchValue !== '') {
      $salesOrderQuery->where(function($query) use($searchValue){
          if ($searchValue != '') {
            $query->orWhere('customer.names', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.surnames', 'like', '%'.$searchValue.'%');
            $query->orWhere('customerIdentification', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.comercialName', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.businessName', 'like', '%'.$searchValue.'%');
            $query->orWhere('status', 'like', '%'.$searchValue.'%');
            $query->orWhere('number', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.code', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.name', 'like', '%'.$searchValue.'%');
            $query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.total', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.iva', 'like', '%'.$searchValue.'%');
          }
      });
    }  

    $salesOrders = $salesOrderQuery
      ->with('customer')
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $recordsFiltered = $salesOrders->count();

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesOrders
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

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('014');
    $approvalStatus = ApprovalDocument::getApprovalStatus('014');

    $salesOrderQuery = SalesOrder::warehouse()
      ->where(function($query) use($prevStatus, $approvalStatus){
        if ($prevStatus != '') {
          $query->where('status', '=', $prevStatus);
        }
        $query->where('status', '!=', $approvalStatus);
      })
      ->whereIn('status', ['Pendiente de Aprobación']);

    $totalRecords = $salesOrderQuery->count(); 

    if ($searchValue !== '') {
      $salesOrderQuery->where(function($query) use($searchValue){
          if ($searchValue != '') {
            $query->orWhere('customer.names', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.surnames', 'like', '%'.$searchValue.'%');
            $query->orWhere('customerIdentification', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.comercialName', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.businessName', 'like', '%'.$searchValue.'%');
            $query->orWhere('status', 'like', '%'.$searchValue.'%');
            $query->orWhere('number', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.code', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.name', 'like', '%'.$searchValue.'%');
            $query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.total', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.iva', 'like', '%'.$searchValue.'%');
          }
      });
    }  

    $salesOrders = $salesOrderQuery
      ->with('customer')
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $recordsFiltered = $salesOrders->count();

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesOrders
    ];

    return $returnData;
  }

  public function forTemporaryStockCustomerInvoice()
  {
    $parameter = Input::all();
    $salesOrder = SalesOrder::warehouse()->where($parameter['parameter'], '=', $parameter['value'])
      ->whereIn('status', ['Recibido completo', 'Recibido parcial', 'Venta parcial'])
      ->get();

    return $salesOrder;
  }

  public function forCustomerInvoiceForTable()
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
    $customer_id = (isset($params['customer_id']) ? $params['customer_id'] : '');

    $searchValue = $params['search']['value'];

    $searchValue = $params['search']['value'];

    $salesOrderQuery = SalesOrder::warehouse()
      ->where('customer_id', '=', $customer_id)
      ->whereIn('status', ['Venta completa', 'Venta parcial', 'Abierto', 'Recibido completo', 'Recibido parcial', 'Facturado parcial']);

    $totalRecords = $salesOrderQuery->count(); 

    if ($searchValue !== '') {
      $salesOrderQuery->where(function($query) use($searchValue){
          if ($searchValue != '') {
            $query->orWhere('customer.names', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.surnames', 'like', '%'.$searchValue.'%');
            $query->orWhere('customerIdentification', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.comercialName', 'like', '%'.$searchValue.'%');
            $query->orWhere('customer.businessName', 'like', '%'.$searchValue.'%');
            $query->orWhere('status', 'like', '%'.$searchValue.'%');
            $query->orWhere('number', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.code', 'like', '%'.$searchValue.'%');
            $query->orWhere('products.name', 'like', '%'.$searchValue.'%');
            $query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.total', 'like', '%'.$searchValue.'%');
            $query->orWhere('totals.iva', 'like', '%'.$searchValue.'%');
          }
      });
    }  

    $salesOrders = $salesOrderQuery
      ->with('customer')
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $recordsFiltered = $salesOrders->count();

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesOrders
    ];

    return $returnData;
  }

  public function forCustomerInvoice()
  {
    $parameter = Input::all();
    $salesOrder = SalesOrder::warehouse()->where($parameter['parameter'], '=', $parameter['value'])
      ->whereIn('status', ['Venta completa', 'Venta parcial', 'Abierto', 'Recibido completo', 'Recibido parcial', 'Facturado parcial'])
      ->get();

    return $salesOrder;

  }

  public function specificData()
  {
    $colums = Input::all();
    $salesOrders = SalesOrder::warehouse()->orderBy('number', 'asc')->get($colums);

    return $salesOrders;
  }

  public function approval($id)
  {
    $salesOffer = SalesOrder::find($id);
    $newData = Input::all();
    $approvalStatus = ApprovalDocument::getApprovalStatus('014');
    $newData['status'] = ($approvalStatus == '' ? 'Aprobado' : $approvalStatus);
    if($salesOffer->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function rejected($id)
  {
    $salesOffer = SalesOrder::find($id);
    $newData = Input::all();
    $rejectedStatus = ApprovalDocument::getRejectedStatus('014');
    $newData['status'] = $rejectedStatus == '' ? 'Rechazado' : $rejectedStatus;
    if($salesOffer->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function store()
  {
    $newSalesOrder = Input::get();
    $newSalesOrder['number'] = $this->generateSalesOrderNumber();
    $customer = Customer::find($newSalesOrder['customer_id']);
    $newSalesOrder['customerIdentification'] = $customer->identification;
    $newSalesOrder['customer'] = $customer->toArray();
    if(SalesOrder::create($newSalesOrder)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function generateFromSalesOffer()
  {
    $newSalesOrder = Input::get();
    $newSalesOrder['number'] = $this->generateSalesOrderNumber();
    $customer = Customer::find($newSalesOrder['customer_id']);
    $newSalesOrder['customerIdentification'] = $customer->identification;
    $newSalesOrder['customer'] = $customer->toArray();
    if(SalesOrder::create($newSalesOrder)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->updateSalesOffer($newSalesOrder);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateSalesOffer($newSalesOrder)
  {
    $salesOffer = SalesOffer::warehouse()->where('number', '=', $newSalesOrder['documentFromNumber'])->first();
    $salesOffer->status = 'Pedido de cliente generado';
    $salesOffer->modelToName = 'SalesOrder';
    $salesOffer->documentToName = 'salesOrder';
    $salesOffer->documentToNumber = $newSalesOrder['number'];
    $salesOffer->save();
  }

  public function update($id)
  {
    $SalesOrder = SalesOrder::find($id);
    $newData = Input::all();
    if($SalesOrder->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $savedSalesOrder = SalesOrder::find($id);
    $annulDate = new \DateTime();
    $savedSalesOrder->annulDate = $annulDate->format('Y-m-d H:i:s');
    $savedSalesOrder->status = 'Anulado';
    if ($savedSalesOrder->save()) {
      AnnulProcess::annul($savedSalesOrder);
      return ResultMsgMaker::annulSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function printDocument()
  {
    $salesOrder = SalesOrder::where('number', Input::get('number'))->first();
    $customer = Customer::find($salesOrder['customer_id']);
    $customerName = $this->getNameOfCustomer($customer->toArray());
    $companyInfo = CompanyInfo::first();
    $salesOfferDetails = $salesOrder->details ? $salesOrder->details : '';
    $seller = '';
    $data = [
      'companyInfo' => $companyInfo,
      'document' => $salesOrder->toArray(),
      'documentDetails' => $salesOfferDetails,
      'customer' => $customer->toArray(),
      'customerName' => $customerName,
      'seller' => $seller,
      'products' => $salesOrder['products']
    ];

    $pdf = \PDF::loadView('pdf.salesOrder', $data)->setPaper('a4');
    $path = SystemConfiguration::getPublicCompanyPath().'prints/';
    if (!\Storage::exists($path)) {
      \Storage::makeDirectory($path);
    }

    $pdf->save(SystemConfiguration::getPublicCompanyPath(true).'prints/salesOrder.pdf');
    return ['url'=> SystemConfiguration::getPublicCompanyPath().'prints/salesOrder.pdf'];
  }

  private function getNameOfCustomer($customer)
  {
    if (isset($customer['comercialName'])) {
      return $customer['comercialName'];
    }

    if (isset($customer['bussinessName'])) {
      return $customer['comercialName'];
    }

    $names = '';
    $surnames = '';
    if (isset($customer['names'])) {
      $names = strtoupper($customer['names']);
    }
    if (isset($customer['surnames'])) {
      $surnames = strtoupper($customer['surnames']);
    }
    return strtoupper($names." ".$surnames);
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '014')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateSalesOrderNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
