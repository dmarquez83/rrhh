<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\GeneralJournal;
use App\Models\CustomerInvoice;
use App\Models\SalesCreditNote;
use App\Models\SalesDebitNote;
use App\Models\SupplierInvoice;
use App\Models\SalesRetention;
use App\Models\PurchaseRetention;
use App\Models\PurchaseCreditNote;
use App\Helpers\ResultMsgMaker;
use App\Helpers\Majorization;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for GeneralJournal Model
|--------------------------------------------------------------------------
*/


class GeneralJournalController extends Controller {

  public function index()
  {
    $journals = GeneralJournal::orderBy('number', 'asc')->get();
    return $journals;
  }

  public function forTable()
  {

    $params = Input::all();

    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $startDate = isset($params['startDate']) ? $params['startDate'] : '';
    $endDate = isset($params['endDate']) ? $params['endDate'] : '';

    $columOrderIndex = $params['order'][0]['column'];
    $columOrderDir = $params['order'][0]['dir'];
    $columOrderName = $params['columns'][$columOrderIndex]['data'];

    $searchValue = $params['search']['value'];

    $totalRecords = GeneralJournal::count();
    $recordsFiltered = $totalRecords;
    $customers = GeneralJournal::skip($start)
      ->where(function($query) use($startDate, $endDate){
        if ($startDate != '' && $endDate != ''){
          $query->whereBetween('date', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))]);
        }
      })
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

  public function store()
  {
    $accountingEntry = Input::all();
    $accountingEntry['number'] = $this->getEntryNumber();
    $finalGeneralJournal = GeneralJournal::create($accountingEntry);
    if ($finalGeneralJournal) {
      Majorization::processGeneralJournalEntry($finalGeneralJournal['_id']);
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }

  }

  private function getEntryNumber()
  {
    $lastJournal = GeneralJournal::orderby('created_at', 'desc')->first();
    return  (int) $lastJournal->number;
  }

  public function save($accountingEntries)
  {
    foreach ($accountingEntries as $key => $entry) {
      $totalEntries = GeneralJournal::count();
      $accountingEntries[$key]['number'] = $totalEntries + 1;
      $finalGeneralJournal = GeneralJournal::create($accountingEntries[$key]);
      if ($key == 1) {
        $this->updateDocumentWithCostGeneralJournalEntryNumber($accountingEntries[$key]);
      } else {
        $this->updateDocumentWithGeneralJournalEntryNumber($accountingEntries[$key]);
      }
      $majorization = new Majorization();
      $majorization->processGeneralJournalEntry($finalGeneralJournal['_id']);
    }
  }

  private function updateDocumentWithGeneralJournalEntryNumber($journalEntry)
  {
    $document = [];
    if ($journalEntry['documentCode'] === '001') {
      $document = CustomerInvoice::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '004') {
      $document = SalesRetention::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '015') {
      $document = PurchaseRetention::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '012') {
      $document = SupplierInvoice::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '019') {
      $document = SalesCreditNote::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '024') {
      $document = SalesDebitNote::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '002') {
      $document = PurchaseCreditNote::find($journalEntry['document_id']);
    }
    $document->generalJournalEntryNumber = $journalEntry['number'];
    $document->save();
  }

  private function updateDocumentWithCostGeneralJournalEntryNumber($journalEntry)
  {
    $document = [];
    if ($journalEntry['documentCode'] === '001') {
      $document = CustomerInvoice::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '019') {
      $document = SalesCreditNote::find($journalEntry['document_id']);
    } else if ($journalEntry['documentCode'] === '024') {
      $document = SalesDebitNote::find($journalEntry['document_id']);
    }
    $document->costGeneralJournalEntryNumber = $journalEntry['number'];
    $document->save();

  }

  public function searchByParameters()
  {
    $parameters = Input::all();

    $startDate = $parameters['startDate']." 00:00:00";
    $endDate = $parameters['endDate']." 23:59:59";

    $generalJournalEntries = GeneralJournal::whereBetween('date', [$startDate, $endDate])->get();
    return $generalJournalEntries;

  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $generalJournal = GeneralJournal::where($parameter['parameter'], '=', $parameter['value'])->get();
    return $generalJournal;
  }

}
