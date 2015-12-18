<?php namespace App\Http\Controllers;

use App\Helpers\AccessPassword;
use App\Helpers\ElectronicRetention;
use App\Helpers\SystemConfiguration;
use App\Models\DocumentConfiguration;
use App\Models\PurchaseRetention;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use App\Helpers\GeneralJournalMaker;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for PurchaseRetention Model
|--------------------------------------------------------------------------
*/

class PurchaseRetentionController extends Controller {


	public function index()
	{
		//
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

    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $salesNumberFrom = (isset($params['salesRetentionNumberFrom']) ? $params['salesRetentionNumberFrom'] : '');
    $salesNumberUntil = (isset($params['salesRetentionNumberUntil']) ? $params['salesRetentionNumberUntil'] : '');
    $selectedSuppliers = (isset($params['selectedSuppliers']) ? $params['selectedSuppliers'] : []);
    $selectedStatus = (isset($params['selectedStatus']) ? $params['selectedStatus'] : []);

    $searchValue = $params['search']['value'];

    $totalRecords = PurchaseRetention::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $purchaseRetentions = PurchaseRetention::warehouse()
      ->where(function($query) use($startDate, $endDate, $salesNumberFrom, $salesNumberUntil, $selectedSuppliers, $selectedStatus){
        if($startDate != '' && $endDate != ''){
          $query->whereBetween('creationDate', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
        if($salesNumberFrom != '' && $salesNumberUntil != ''){
          $query->whereBetween('number', [$salesNumberFrom, $salesNumberUntil]);
        }
        if(count($selectedSuppliers) > 0) {
          $query->whereIn('supplierIdentification', $selectedSuppliers);
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
      $purchaseRetentions = $purchaseRetentions->filter(function($purchaseRetention) use($searchValue){
        if (stripos($purchaseRetention, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchaseRetentions->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchaseRetentions);
    return $returnData;
  }

  public function specificData()
  {
    $colums = Input::all();
    $purchaseRetention = PurchaseRetention::orderBy('number', 'asc')->get($colums);

    return $purchaseRetention;
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

    $totalRecords = PurchaseRetention::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = PurchaseRetention::warehouse()
      ->whereIn('status', ['Pendiente', 'Pendiente de pago'])
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


	public function create()
	{
		//
	}

	public function store()
  {
    $purchaseRetention = Input::get();
    $purchaseRetention['status'] = 'Enviada';
    $purchaseRetention['number'] = $this->generatePurchaseRetentionNumber();
    $savedPurchaseRetention = PurchaseRetention::create($purchaseRetention);
    if ($savedPurchaseRetention) {
      $this->updateSecuencial();
      $savedPurchaseRetention->accessPasword = AccessPassword::generate($purchaseRetention['number'], 'App\Models\PurchaseRetention');
      $savedPurchaseRetention->save();

			if (SystemConfiguration::isElectronicDocumentsEnabled()) {
				$savedPurchaseRetention->electronicStatus = 'ENVIADO';
				$savedPurchaseRetention->save();

				$path = base_path().'/artisan';
	      $number = $savedPurchaseRetention->number;
	      $warehouseId = $savedPurchaseRetention->warehouse_id;
	      $cmd = "php $path generateElectronicRetention '$number' '$warehouseId'";
	      $this->execInBackground($cmd);
			}

      if (SystemConfiguration::isAccountingEnabled()) {
        GeneralJournalMaker::generateEntryFromRetention($savedPurchaseRetention, '015');
      }
      return ResultMsgMaker::saveSuccessWithExtraData(['number' => $savedPurchaseRetention['number'], 'accessPasword' => $savedPurchaseRetention->accessPasword]);
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $purchaseRetention = PurchaseRetention::find($id);
    $newData = Input::all();
    $newData['status'] = 'Pendiente de pago';
    if($purchaseRetention->update($newData)) {
      if (isset($purchaseRetention['payWays'])) {
        GeneralJournalMaker::generateEntryFromRetention($purchaseRetention, '015');
        $purchaseRetention->status = 'Pagado';
        $purchaseRetention->save();
      }
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function save($purchaseRetention)
  {
    $purchaseRetention['creationDate'] = date("Y-m-d H:i:s");
    $purchaseRetention['number'] = $this->generatePurchaseRetentionNumber();
    if(PurchaseRetention::create($purchaseRetention)){
      $this->updateSecuencial();
      return true;
    }
    return false;
  }

  private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '015')
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

	public function downloadFile()
	{
		$purchaseRetentionData = Input::all();

		$purchaseRetention = PurchaseRetention::where('number', '=', $purchaseRetentionData['number'])->first();
		$fechaDocumento = new \DateTime($purchaseRetention->creationDate);
		$fechaFormateada = $fechaDocumento->format('dmY_His');

		if ($purchaseRetentionData['type'] == 'pdf') {
			$electronicPath = SystemConfiguration::getElectronicDocumentsPath();
			$filePath = $electronicPath.'retencion_'.$fechaFormateada.'_ride.pdf';
			$fileNameDownload = SystemConfiguration::getPublicCompanyPath().'download_file.pdf';
			if(\Storage::exists($fileNameDownload)){
				\Storage::delete($fileNameDownload);
			}
			\Storage::copy($filePath, $fileNameDownload);
			return str_replace("public/", "", $fileNameDownload);
		} else if ($purchaseRetentionData['type'] == 'xml') {
			$electronicPath = SystemConfiguration::getElectronicDocumentsPath(true);
			$filePath = $electronicPath.'retencion_'.$fechaFormateada.'_autorizado.xml';
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

  private function cleanString($texto)
  {
    $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);
    return $textoLimpio;
  }

	public function resendElectronicDocument()
  {
    $electronicDocument = Input::all();

    if (SystemConfiguration::isElectronicDocumentsEnabled()) {
      $path = base_path().'/artisan';
      $number = $electronicDocument['documentNumber'];
      $warehouseId = $electronicDocument['warehouse_id'];
      $cmd = "php $path generateElectronicRetention '$number' '$warehouseId'";
      $this->execInBackground($cmd);
      return ResultMsgMaker::successCustom('El documento ha sido reenviado');
    }
    return ResultMsgMaker::erroCustom('No esta activado facturación electronica');
  }

	private function execInBackground($cmd) {
		if (substr(php_uname(), 0, 7) == "Windows"){
			pclose(popen("start /B ". $cmd, "r"));
		}
		else {
			exec($cmd . " > /dev/null &");
		}
	}




}
