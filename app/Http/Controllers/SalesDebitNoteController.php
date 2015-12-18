<?php namespace App\Http\Controllers;


use App\Helpers\GeneralJournalMaker;
use App\Helpers\KardexMaker;
use App\Helpers\ResultMsgMaker;
use App\Models\SalesDebitNote;
use App\Models\CustomerInvoice;
use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SalesDebitNote Model
|--------------------------------------------------------------------------
*/

class SalesDebitNoteController extends Controller {

  private $documentConfiguration;

  public function index()
  {

  }

  public function store()
  {
    $newSalesDebitNote = Input::all();
    $newSalesDebitNote['number'] = $this->generateSalesDebitNoteNumber();
    $savedSaleDebitNote = SalesDebitNote::create($newSalesDebitNote);
    if($savedSaleDebitNote){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();

      GeneralJournalMaker::generateEntry($savedSaleDebitNote->toArray(), '024');
      \App::make('App\Http\Controllers\GoodsReceiptController')->storeFromSalesCreditNote($savedSaleDebitNote->toArray());
      $this->updateCustomerInvoice($newSalesDebitNote['customerInvoiceNumber'], $newSalesDebitNote['number']);

      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateCustomerInvoice($customerInvoiceNumber, $salesCreditNoteNumber)
  {
    $customerInvoice = CustomerInvoice::warehouse()->where('number', '=', $customerInvoiceNumber)->first();
    $customerInvoice->salesDebitNoteNumber = $salesCreditNoteNumber;
    $customerInvoice->save();
  }

  public function destroy($id)
  {
    if(SalesDebitNote::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '024')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateSalesDebitNoteNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
