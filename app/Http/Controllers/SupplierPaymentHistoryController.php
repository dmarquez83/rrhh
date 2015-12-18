<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\DocumentConfiguration;
use App\Models\SupplierPay;
use App\Models\SupplierPaymentHistory;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SupplierPaymentHistory Model
|--------------------------------------------------------------------------
*/

class SupplierPaymentHistoryController extends Controller {

  private $documentConfiguration;


  public function index()
  {
    
  }

  public function store()
  {
    $payments = Input::all();
    $payments['number'] = $this->generateCustomerPaymentNumber();
    if(SupplierPaymentHistory::create($payments)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->updateSupplierPays($payments['pays'], $payments['selectedTotal']);

      GeneralJournalMaker::generateJournalEntry($payments, '018');

      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::erro();
    }
    
  }

  public function updateSupplierPays($pays, $selectedTotal)
  {
    foreach ($pays as $key => $pay) {
      $savedPay = SupplierPay::find($pay);
      $savedPay->paid += $selectedTotal;
      $savedPay->pendingPayment -= $selectedTotal;
      if($savedPay->pendingPayment == 0){
        $savedPay->status = 'Pagada';
      }
      $savedPay->save();
    }
  }

  private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '018')
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
