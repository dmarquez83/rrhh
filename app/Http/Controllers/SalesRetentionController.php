<?php namespace App\Http\Controllers;


use App\Helpers\DataValidator;
use App\Models\DocumentConfiguration;
use App\Models\SalesRetention;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use App\Helpers\GeneralJournalMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SalesRetention Model
|--------------------------------------------------------------------------
*/

class SalesRetentionController extends Controller {

	public function index()
	{
		
	}

  public function forTable()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $salesNumberFrom = (isset($params['salesRetentionNumberFrom']) ? $params['salesRetentionNumberFrom'] : '');
    $salesNumberUntil = (isset($params['salesRetentionNumberUntil']) ? $params['salesRetentionNumberUntil'] : '');
    $selectedCustomers = (isset($params['selectedCustomers']) ? $params['selectedCustomers'] : []);
    $selectedStatus = (isset($params['selectedStatus']) ? $params['selectedStatus'] : []);

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = SalesRetention::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = SalesRetention::warehouse()
      ->where(function($query) use($startDate, $endDate, $salesNumberFrom, $salesNumberUntil, $selectedCustomers, $selectedStatus){
        if($startDate != '' && $endDate != ''){
          $query->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if($salesNumberFrom != '' && $salesNumberUntil != ''){
          $query->whereBetween('number', [$salesNumberFrom, $salesNumberUntil]);
        }
        if(count($selectedCustomers) > 0) {
          $query->whereIn('customerIdentification', $selectedCustomers);
        }
        if(count($selectedStatus) > 0) {
          $query->whereIn('status', $selectedStatus);
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

  public function forTablePending()
  {
    $params = Input::all();
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = SalesRetention::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $salesRetentions = SalesRetention::warehouse()
      ->whereIn('status', ['Pendiente', 'Pendiente de pago'])
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $salesRetentions = $salesRetentions->filter(function($salesRetention) use($searchValue){
        if (stripos($salesRetention, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $salesRetentions->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $salesRetentions);
    return $returnData;
  }

  public function specificData()
  {
    $colums = Input::all();
    $salesRetentionsData = SalesRetention::warehouse()->orderBy('number', 'asc')->get($colums);

    return $salesRetentionsData;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $salesRetention = SalesRetention::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $salesRetention;
  }


	public function create()
	{
		
	}

	public function store()
  {
    $salesRetention = Input::get();
    $salesRetention['status'] = 'Pendiente de pago';
    $salesRetention['number'] = $this->generatePurchaseRetentionNumber();
    $savedSalesRetention = SalesRetention::create($salesRetention);
    if($savedSalesRetention){
      $this->updateSecuencial();
      if (isset($salesRetention['payWays'])) {
        GeneralJournalMaker::generateEntryFromRetention($salesRetention, '004');
        $savedSalesRetention->status = 'Pagado';
        $savedSalesRetention->save();
      }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $salesRetention = SalesRetention::find($id);
    $newData = Input::all();
    $newData['status'] = 'Pendiente de pago';
    if($salesRetention->update($newData)) {
      if (isset($salesRetention['payWays'])) {
        GeneralJournalMaker::generateEntryFromRetention($salesRetention, '004');
        $salesRetention->status = 'Pagado';
        $salesRetention->save();
      }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  public function save($salesRetention)
  { 
    $salesRetention['creationDate'] = date("Y-m-d H:i:s");
    $salesRetention['number'] = $this->generatePurchaseRetentionNumber();
    if(SalesRetention::create($salesRetention)){
      $this->updateSecuencial();
      return true;
    }
    return false;
  }


  private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '004')
      ->where('warehouseId', '=', Session::get('warehouseId'))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }


  private function updateSecuencial()
  {
    $this->documentConfiguration->secuencial += 1;
    $this->documentConfiguration->save();
  }


  private function generatePurchaseRetentionNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
