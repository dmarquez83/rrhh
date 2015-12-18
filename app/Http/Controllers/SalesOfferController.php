<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\ApprovalDocument;
use App\Models\DocumentConfiguration;
use App\Models\SalesOffer;
use App\Models\Customer;
use App\Models\CompanyInfo;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use App\Helpers\SystemConfiguration;
use App\Helpers\BusinessPartner;
use Illuminate\Support\Facades\Input;
use MongoId;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SalesOffer Model
|--------------------------------------------------------------------------
*/

class SalesOfferController extends Controller {

 public function index()
  {
    $salesOffers = SalesOffer::warehouse()->with('customer');
    return $salesOffers;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $salesOffer = SalesOffer::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $salesOffer;
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

    $totalRecords = SalesOffer::warehouse()
    ->where(function($query) use($startDate, $endDate, $status, $salesNumberFrom, $salesNumberUntil, $selectedProducts, $selectedCustomers){
      if(count($status) > 0) {
        $query->whereRaw(['status' => ['$in' => $status]]);
      }
      if($startDate != '' && $endDate != '') {
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
    })
    ->count();
    $recordsFiltered = $totalRecords;

    
    if ($searchValue !== '') {
      $salesOffers = SalesOffer::warehouse()
        ->with('customer', 'payment', 'employee')
        ->where(function($query) use($startDate, $endDate, $status, $salesNumberFrom, $salesNumberUntil, $selectedProducts, $selectedCustomers){
          if(count($status) > 0) {
            $query->whereRaw(['status' => ['$in' => $status]]);
          }
          if($startDate != '' && $endDate != '') {
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
        })
        ->where(function($query) use($searchValue){
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
        })
        ->skip($start)
        ->take($length)
        ->orderBy($columOrderName, $columOrderDir)
        ->get();
        $recordsFiltered = $salesOffers->count();
    } else {
      $salesOffers = SalesOffer::warehouse()
        ->with('customer', 'payment', 'employee')
        ->where(function($query) use($startDate, $endDate, $status, $salesNumberFrom, $salesNumberUntil, $selectedProducts, $selectedCustomers){
          if(count($status) > 0) {
            $query->whereRaw(['status' => ['$in' => $status]]);
          }
          if($startDate != '' && $endDate != '') {
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
        })
        ->skip($start)
        ->take($length)
        ->orderBy($columOrderName, $columOrderDir)
        ->get();

    }


    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesOffers];
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

    $totalRecords = SalesOffer::warehouse()->count();
    $recordsFiltered = $totalRecords;

    $prevStatus = ApprovalDocument::getPrevApprovalStatus('013');
    $approvalStatus = ApprovalDocument::getApprovalStatus('013');

    $salesOffers = SalesOffer::warehouse()
      ->where(function($query) use($prevStatus, $approvalStatus){
        if($prevStatus != '') {
          $query->where('status', '=', $prevStatus);
        }
        $query->where('status', '!=', $approvalStatus);
      })
      ->whereNotIn('status', ['Aprobado', 'Abierto', 'Rechazado', 'Anulado', 'Pedido de cliente generado', 'Factura generada'])
      ->skip($start)
      ->with('customer')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $salesOffers = $salesOffers->filter(function($salesOffer) use($searchValue){
        if (stripos($salesOffer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $salesOffers->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesOffers);
    return $returnData;
  }

  public function specificData()
  {
    $colums = Input::all();
    $salesOrders = SalesOffer::warehouse()->orderBy('number', 'asc')->get($colums);

    return $salesOrders;
  }

  public function store()
  {
    $newSalesOffer = Input::get();
    $newSalesOffer['number'] = $this->generateSalesOfferNumber();
    $customer = Customer::find($newSalesOffer['customer_id']);
    $newSalesOffer['customerIdentification'] = $customer->identification;
    $newSalesOffer['customer'] = $customer->toArray();
    if(SalesOffer::create($newSalesOffer)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $extraData = ['salesOfferNumber' => $newSalesOffer['number']];
      return ResultMsgMaker::saveSuccessWithExtraData($extraData);
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function approval($id)
  {
    $salesOffer = SalesOffer::find($id);
    $newData = Input::all();
    $approvalStatus = ApprovalDocument::getApprovalStatus('013');
    $newData['status'] = ($approvalStatus == '' ? 'Aprobado' : $approvalStatus);
    if($salesOffer->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function rejected($id)
  {
    $salesOffer = SalesOffer::find($id);
    $newData = Input::all();
    $rejectedStatus = ApprovalDocument::getRejectedStatus('013');
    $newData['status'] = $rejectedStatus == '' ? 'Rechazado' : $rejectedStatus;
    if($salesOffer->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $SalesOffer = SalesOffer::find($id);
    $newData = Input::all();
    if($SalesOffer->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $savedSalesOffer = SalesOffer::find($id);
    $annulDate = new \DateTime();
    $savedSalesOffer->annulDate = $annulDate->format('Y-m-d H:i:s');
    $savedSalesOffer->status = 'Anulado';
    if ($savedSalesOffer->save()) {
      return ResultMsgMaker::annulSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function printDocument()
  {
    $salesOffer = SalesOffer::warehouse()->where('number', Input::get('number'))->first();
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

    $pdf = \PDF::loadView('pdf.salesOffer', $data)->setPaper('a4');
    $path = SystemConfiguration::getPublicCompanyPath().'prints/';
    if (!\Storage::exists($path)) {
      \Storage::makeDirectory($path);
    }
    $pdf->save(SystemConfiguration::getPublicCompanyPath(true).'prints/salesOffer.pdf');
    return ['url'=> SystemConfiguration::getPublicCompanyPath().'prints/salesOffer.pdf'];
  }


  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '013')
      ->where('warehouse_id', '=', new MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateSalesOfferNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
