<?php namespace App\Http\Controllers;


use App\Helpers\GeneralJournalMaker;
use App\Helpers\KardexMaker;
use App\Helpers\ResultMsgMaker;
use App\Models\PurchaseCreditNote;
use App\Models\SupplierInvoice;
use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for SalesCreditNote Model
|--------------------------------------------------------------------------
*/

class PurchaseCreditNoteController extends Controller {

  private $documentConfiguration;

  public function index()
  {
    
  }

  public function store()
  {
    $newPurchaseCreditNote = Input::all();
    $newPurchaseCreditNote['number'] = $this->generatePurchaseCreditNoteNumber();
    $savedPurchaseCreditNote = PurchaseCreditNote::create($newPurchaseCreditNote);
    if($savedPurchaseCreditNote){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();

      GeneralJournalMaker::generateEntry($savedPurchaseCreditNote->toArray(), '002');
      \App::make('App\Http\Controllers\GoodsDeliveryController')->storeFromPurchaseCreditNote($savedPurchaseCreditNote->toArray());
      $this->updateSupplierInvoice($newPurchaseCreditNote['supplierInvoiceNumber'], $newPurchaseCreditNote['number']);

      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateSupplierInvoice($supplierInvoiceNumber, $purchaseCreditNoteNumber)
  {
    $customerInvoice = SupplierInvoice::warehouse()->where('number', '=', $supplierInvoiceNumber)->first();
    $customerInvoice->purchaseCreditNoteNumber = $purchaseCreditNoteNumber;
    $customerInvoice->save();
  }

  public function destroy($id)
  {
    if(PurchaseCreditNote::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }


  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '002')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;              

    return $newSecuencial;             
  }

  private function generatePurchaseCreditNoteNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
