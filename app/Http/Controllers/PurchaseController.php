<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Configuration;
use App\Models\Purchase;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class PurchaseController extends Controller {

  protected $purchaseAccounts;
  protected $stocktakingAccounts;
  protected $incomeTaxesAccounts;
  protected $payableIRFIncomeTaxesAccounts;
  protected $payableIVARetainerAccounts;

  public function index()
  {
    $purchases = Purchase::all();

    return $purchases;
  }

  public function store()
  {
    $newPurchase = Input::get('Purchase');
    $result = array(
      'type' => 'success',
      'msg' => 'Se guardo el documento correctamente');

    if (!Purchase::create($newPurchase)) {
      $result['type'] = 'danger';
      $result['msg'] = 'Ocurrio un Problema al guardar el documento';
      return $result;
    }

    $this->getFinancialAccounts();
    $journalEntry = $this->prepareJournalEntry($newPurchase);
    $journalEntries = [$journalEntry];
    App::make('GeneralJournalController')->save($journalEntries);

    $kardexData = $this->prepareKardexData($newPurchase);
    App::make('KardexController')->store($kardexData);

    return $result;
  }

  protected function getFinancialAccounts()
  {
    $financialAccounts = Configuration::first();
    $this->purchaseAccounts = $financialAccounts->purchasing;
    $this->stocktakingAccounts = $financialAccounts->stocktaking;
    $this->payableIRFIncomeTaxesAccounts = $financialAccounts->payableIRFIncomeTaxesAccounts;
    $this->payableIVARetainerAccounts = $financialAccounts->payableIVARetainerAccounts;
  }

  protected function prepareJournalEntry($purchase)
  {
    $jorunalEntry =  array();
    $jorunalEntry['date'] = $purchase['date'];
    $jorunalEntry['debit'] = array();
    $jorunalEntry['credit'] = array();

    if($purchase['type'] == 'goodPurchase') {
      $data = array(
        'accountName' => $this->stocktakingAccounts['goods']['name'],
        'code' => $this->stocktakingAccounts['goods']['code'],
        'amount' => $purchase['total'] - $purchase['iva']
      );
      array_push($jorunalEntry['debit'], $data);
      $purchase['type'] = 'Compra Mercaderia';
    }

    if($purchase['type'] == 'purchaseInvoice') {
      $data = array(
        'accountName' => $this->purchaseAccounts['commodity']['name'],
        'code' => $this->purchaseAccounts['commodity']['code'],
        'amount' => $purchase['subtotalIva'] + $purchase['subtotal']
      );
      array_push($jorunalEntry['debit'], $data);
    }

    if ($purchase['iva'] > 0) {
      $data = array(
        'accountName' => $this->purchaseAccounts['iva']['name'],
        'code' => $this->purchaseAccounts['iva']['code'],
        'amount' => $purchase['iva']
      );
      array_push($jorunalEntry['debit'], $data);
    } 
    
    $valorImpuestos = 0;

    if(count($purchase['incomeTaxes']) > 0) {
      foreach ($purchase['incomeTaxes'] as $key => $incomeTax) {
        $idTax = $incomeTax['taxConcept']['_id'];
        $taxAccount = (isset($this->payableIRFIncomeTaxesAccounts[$idTax]) ? 
                      $this->payableIRFIncomeTaxesAccounts[$idTax] : 
                      $this->payableIVARetainerAccounts[$idTax]);

        $taxAmount = $incomeTax['totalAmount'];
        $data = array(
          'accountName' => $taxAccount['name'],
          'code' => $taxAccount['code'],
          'amount' => $taxAmount
        );
        $valorImpuestos += $taxAmount;
        array_push($jorunalEntry['credit'], $data);
      }
    }

    if($purchase['cash']){



      $purchase['cashData']['amount'] -= $valorImpuestos;
      $valorImpuestos = 0;

      $data = array(
        'accountName' => $this->purchaseAccounts['cash']['name'],
        'code' => $this->purchaseAccounts['cash']['code'],
        'amount' => $purchase['cashData']['amount']
      );
      array_push($jorunalEntry['credit'], $data);
    }


    if($purchase['credit']){
      if ($valorImpuestos > 0){
        $purchase['creditData']['amount']-= $valorImpuestos;
        $valorImpuestos = 0;
      }
      $data = array(
        'accountName' => $this->purchaseAccounts['creditAccount']['name'],
        'code' => $this->purchaseAccounts['creditAccount']['code'],
        'amount' => $purchase['creditData']['amount']
      );
      array_push($jorunalEntry['credit'], $data);
    }

    

    $jorunalEntry['description'] = "Ref. Para registrar compra s/".$purchase['type']." No".$purchase['number'];

    return $jorunalEntry;
  }

  protected function prepareKardexData($purchase)
  {

    $kardexData = array();

    foreach ($purchase['products'] as $key => $product) {
      $kardexData[$product['code']]['productCode'] = $product['code'];
      $kardexData[$product['code']]['valuationMethod'] = $product['stocktaking']['valuationMethod'];
      $kardexData[$product['code']]['productName'] = $product['name'];
      $kardexData[$product['code']]['movements'][$key]['date'] = $purchase['date'];
      $concept = 'Registro s/ '.$purchase['type']." ".$purchase['number'];
      $kardexData[$product['code']]['movements'][$key]['concept'] = $concept;
      $kardexData[$product['code']]['movements'][$key]['inputs'] = array(
        'quantity' => $product['sellingQuantity'],
        'unitCost' => $product['sellingPrice'],
        'total' => $product['sellingQuantity'] * $product['sellingPrice']
      );
    }
    return $kardexData;
  }


  public function searchByParameters()
  {
    $searchParameters = Input::all();
    $starDate = $searchParameters['startDate'];
    $endDate = $searchParameters['endDate'];
    $suppliers = (isset($searchParameters['selectedSuppliers']) ? $searchParameters['selectedSuppliers'] : array());
    $numbers = (isset($searchParameters['selectedPurchases']) ? $searchParameters['selectedPurchases'] : array());
    $productCodes = (isset($searchParameters['selectedProducts']) ? $searchParameters['selectedProducts'] : array());

    $draw = $searchParameters['draw'];
    $start = $searchParameters['start'];
    $length = $searchParameters['length'];

    $columOrderIndex = $searchParameters['order'][0]['column'];
    $columOrderDir = $searchParameters['order'][0]['dir'];
    $columOrderName = $searchParameters['columns'][$columOrderIndex]['data'];

    $searchValue = $searchParameters['search']['value'];

    $totalRecords = Purchase::count();
    $recordsFiltered = $totalRecords;

    $purchases = Purchase::whereBetween('date',  array($starDate, $endDate))
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->where(function($query) use($suppliers, $numbers, $productCodes){
        if(count($suppliers) > 0){
          $query->whereRaw(array('supplier.identification' => array('$in' => $suppliers)));
        }

        if(count($numbers) > 0){
          $query->whereIn('number', $numbers);
        }

        if(count($productCodes) > 0){
          $query->whereRaw(array('products.code' => array('$in' => $productCodes)));
        }                 
      })
      ->get();

    if($searchValue!=''){
      $purchases = $purchases->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $purchases->count();
    }  

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $purchases);
    return $returnData;
  }




}
