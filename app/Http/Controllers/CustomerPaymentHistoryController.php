<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\GeneralJournalMaker;
use App\Helpers\ResultMsgMaker;
use App\Models\CustomerPaymentHistory;
use App\Models\CustomerQuotas;
use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for CustomerPaymentHistory Model
|--------------------------------------------------------------------------
*/

class CustomerPaymentHistoryController extends Controller {

  private $documentConfiguration;


	public function index()
	{

	}

	public function store()
	{
    $payments = Input::all();
    $payments['number'] = $this->generateCustomerPaymentNumber();
    if(CustomerPaymentHistory::create($payments)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      //$this->updateCustomerQuotas($payments['quotas'], $payments['selectedTotal']);

      //GeneralJournalMaker::generateJournalEntry($payments, '017');

      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::erro();
    }

	}

  public function updateCustomerQuotas($quotas, $selectedTotal)
  {
    foreach ($quotas as $key => $quota) {
      $savedQuota = CustomerQuotas::find($quota);
      $savedQuota->paid += $selectedTotal;
      $savedQuota->pendingPayment -= $selectedTotal;
      if($savedQuota->pendingPayment == 0){
        $savedQuota->status = 'Pagada';
      }
      $savedQuota->save();
    }
  }

  private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '017')
      ->where('warehouseId', '=', Session::get('warehouseId'))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateCustomerPaymentNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }



}
