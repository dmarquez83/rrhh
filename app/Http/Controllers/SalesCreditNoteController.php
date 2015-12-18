<?php namespace App\Http\Controllers;


use App\Helpers\GeneralJournalMaker;
use App\Helpers\KardexMaker;
use App\Helpers\ResultMsgMaker;
use App\Models\SalesCreditNote;
use App\Models\CustomerInvoice;
use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SalesCreditNote Model
|--------------------------------------------------------------------------
*/

class SalesCreditNoteController extends Controller {

  private $documentConfiguration;

  public function index()
  {
    
  }

  public function store()
  {
    $newSalesCreditNote = Input::all();
    $newSalesCreditNote['number'] = $this->generateSalesCreditNoteNumber();
    $savedSaleCreditNote = SalesCreditNote::create($newSalesCreditNote);
    if($savedSaleCreditNote){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();

      GeneralJournalMaker::generateEntry($savedSaleCreditNote->toArray(), '019');
      \App::make('App\Http\Controllers\GoodsReceiptController')->storeFromSalesCreditNote($savedSaleCreditNote->toArray());
      $this->updateCustomerInvoice($newSalesCreditNote['customerInvoiceNumber'], $newSalesCreditNote['number']);

      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateCustomerInvoice($customerInvoiceNumber, $salesCreditNoteNumber)
  {
    $customerInvoice = CustomerInvoice::warehouse()->where('number', '=', $customerInvoiceNumber)->first();
    $customerInvoice->salesCreditNoteNumber = $salesCreditNoteNumber;
    $customerInvoice->save();
  }

  public function destroy($id)
  {
    if(SalesCreditNote::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '019')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;              

    return $newSecuencial;             
  }

  private function generateSalesCreditNoteNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
