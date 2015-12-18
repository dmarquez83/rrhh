<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use App\Models\DocumentConfiguration;
use App\Models\ReferralGuide;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Transport;
use App\Models\CustomerInvoice;
use App\Models\GoodsDelivery;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for ReferralGuide Model
|--------------------------------------------------------------------------
*/

class ReferralGuideController extends Controller {

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

	    $totalRecords = ReferralGuide::warehouse()->count();
	    $recordsFiltered = $totalRecords;
	    $customers = ReferralGuide::warehouse()
	      ->skip($start)
	      ->take($length)
	      ->orderBy($columOrderName, $columOrderDir)
	      ->get();

	    $returnData = [
	      'draw' => $draw,
	      'recordsTotal' => $totalRecords,
	      'recordsFiltered' => $recordsFiltered,
	      'data' => $customers];
	    return $returnData;
	}

	public function getByParameterPost()
	{
		$parameter = Input::all();
		$referralGuide = ReferralGuide::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
		return $referralGuide;
	}


	public function generateFromCustomerInvoice() 
	{
		$customerInvoice = Input::all();
		$date = new \DateTime();
		$newReferralGuide= [];
		$newReferralGuide['creationDate'] = $date->format('Y-m-d H:i:s');
		$newReferralGuide['status'] = 'Pendiente';
		$newReferralGuide['number'] = $this->generateReferralGuideNumber();
		$newReferralGuide['customerInvoiceNumber'] = $customerInvoice['number'];
    $newReferralGuide['customer_id'] = $customerInvoice['customer']['_id'];
		$newReferralGuide['products'] = $customerInvoice['products'];
		if (ReferralGuide::create($newReferralGuide)) {
			$this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
			$this->updateCustomerInvoice($customerInvoice, $newReferralGuide['number']);
			return ResultMsgMaker::successCustom('Se creó una guía de remisión correctamente');
		} else {
			return ResultMsgMaker::error();
		}
	}

	
	private function updateCustomerInvoice($customerInvoice, $referralGuideNumber)
	{
		$customerInvoice = CustomerInvoice::find($customerInvoice['_id']);
		$customerInvoice->referralGuideNumber = $referralGuideNumber;
		$customerInvoice->save();
	}

	public function store() 
	{
		$newReferralGuide = Input::get();
	    $newReferralGuide['number'] = $this->generateReferralGuideNumber();
	    $customer = Customer::find($newReferralGuide['customer_id']);
	    $driver = Driver::where('identification', '=', $newReferralGuide['driver']['identification'])->first();
	    $transport = Transport::where('plate', '=', $newReferralGuide['transport']['plate'])->first();
	    $referralGuide = ReferralGuide::create($newReferralGuide);
	    if($referralGuide){
	      $this->documentConfiguration->secuencial += 1;
	      $this->documentConfiguration->save();
		  $referralGuide->customer()->create($customer->toArray());
		  $referralGuide->driver()->create($driver->toArray());
		  $referralGuide->transport()->create($transport->toArray());
	      return ResultMsgMaker::saveSuccess();
	    } else {
	      return ResultMsgMaker::error();
	    }
	}

	public function receivedCustomer()
	{
		$newData = Input::all();
		$referralGuide = ReferralGuide::find($newData['_id']);
		$referralGuide->status = 'Recibido Cliente';
		if($referralGuide->save()) {
		  $goodsDelivery = GoodsDelivery::warehouse()->where('referralGuideNumber', '=', $referralGuide->number)->first();
		  $goodsDelivery->status = 'Recibido Cliente';
		  $goodsDelivery->save(); 
		  return ResultMsgMaker::saveSuccess();
		} else {
		  return ResultMsgMaker::error();
		}
	}


	public function update($id)
	{
		$referralGuide = ReferralGuide::find($id);
		$newData = Input::all();
		$newData['status'] = 'Abierto';
		$customer = Customer::find($newData['customer_id']);
	    $driver = Driver::where('identification', '=', $newData['driver']['identification'])->first();
	    $transport = Transport::where('plate', '=', $newData['transport']['plate'])->first();
	    unset($newData['customer']);
	    unset($newData['driver']);
	    unset($newData['transport']);
		if($referralGuide->update($newData)) {
		  $referralGuide->customer()->create($customer->toArray());
		  $referralGuide->driver()->create($driver->toArray());
		  $referralGuide->transport()->create($transport->toArray());
		  return ResultMsgMaker::saveSuccess();
		} else {
		  return ResultMsgMaker::error();
		}
	}

	private function getSecuencial()
	{
		$currentWarehouse = Session::get('currentWarehouse');
		$this->documentConfiguration = DocumentConfiguration::where('code', '=', '005')
		  ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
		$newSecuencial = $this->documentConfiguration->secuencial + 1;

		return $newSecuencial;
	}

	private function generateReferralGuideNumber()
	{
		$number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
		$companySerie = $this->documentConfiguration->companySerie;
		$warehouseSerie = $this->documentConfiguration->warehouseSerie;
		$prefix = $companySerie.'-'.$warehouseSerie.'-';

		return $prefix.$number;
	}


}
