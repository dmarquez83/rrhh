<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\GeneralParameter;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\PurchaseQuotation;
use App\Models\DocumentConfiguration;
use App\Helpers\TemporaryKardexMaker;
use App\Helpers\ResultMsgMaker;
use App\Helpers\HistoryStockProductReserveMaker;
use App\Models\TemporaryStockCustomerInvoice;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for TemporaryStockCustomerInvoice Model
|--------------------------------------------------------------------------
*/

class TemporaryStockCustomerInvoiceController extends Controller {

	private $documentConfiguration;

	public function index()
	{

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
    $status = (isset($params['status']) ? $params['status'] : []);
    $searchValue = $params['search']['value'];

    $totalRecords = TemporaryStockCustomerInvoice::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $temporaryStockCustomerInvoices = TemporaryStockCustomerInvoice::warehouse()
      ->where(function($query) use($startDate, $endDate, $status, $searchValue) {
        if (count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if ($startDate != '' && $endDate != '') {
          $query->whereBetween('date', [$startDate, $endDate]);
        }
				if ($searchValue != '') {
					$query->orWhere('number', 'like', '%'.$searchValue.'%');
					$query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
					$query->orWhere('status', 'like', '%'.$searchValue.'%');
					$query->orWhere('totals', 'like', '%'.$searchValue.'%');
					$query->orWhere('products', 'like', '%'.$searchValue.'%');
					$query->orWhere('customer.identification', 'like', '%'.$searchValue.'%');
					$query->orWhere('customer.names', 'like', '%'.$searchValue.'%');
					$query->orWhere('customer.surnames', 'like', '%'.$searchValue.'%');
				}
      })
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

		if ($searchValue) {
			$recordsFiltered = TemporaryStockCustomerInvoice::warehouse()
				->where(function($query) use($startDate, $endDate, $status, $searchValue) {
					if (count($status) > 0) {
						$query->whereRaw(['status' => ['$in' => $status]]);
					}
					if ($startDate != '' && $endDate != '') {
						$query->whereBetween('date', [$startDate, $endDate]);
					}
					if ($searchValue != '') {
						$query->orWhere('number', 'like', '%'.$searchValue.'%');
						$query->orWhere('creationDate', 'like', '%'.$searchValue.'%');
						$query->orWhere('status', 'like', '%'.$searchValue.'%');
						$query->orWhere('totals', 'like', '%'.$searchValue.'%');
						$query->orWhere('products', 'like', '%'.$searchValue.'%');
						$query->orWhere('customer.identification', 'like', '%'.$searchValue.'%');
						$query->orWhere('customer.names', 'like', '%'.$searchValue.'%');
						$query->orWhere('customer.surnames', 'like', '%'.$searchValue.'%');
					}
				})
				->count();
		}


    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $temporaryStockCustomerInvoices];
    return $returnData;
  }

	public function store()
	{
		$temporaryStockCustomerInvoice = Input::get();
    $temporaryStockCustomerInvoice['number'] = $this->generateCustomerInvoiceNumber();
		$temporaryStockCustomerInvoice['status'] = 'Pendiente de Facturar';
    $temporaryStockCustomerInvoice = TemporaryStockCustomerInvoice::create($temporaryStockCustomerInvoice);
    if ($temporaryStockCustomerInvoice) {
			$customer = Customer::find($temporaryStockCustomerInvoice['customer_id']);
			$temporaryStockCustomerInvoice->customer()->create($customer->toArray());
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
			$this->updateProductSoldQuantityOfSalesOrder($temporaryStockCustomerInvoice['salesOrderNumber'], $temporaryStockCustomerInvoice);
			//$this->reserveStock($temporaryStockCustomerInvoice);
			$this->updateSalesOrderHistory($temporaryStockCustomerInvoice['salesOrderNumber'], $temporaryStockCustomerInvoice['number']);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
	}


	private function updateProductSoldQuantityOfSalesOrder($salesOrderNumber, $temporaryStockCustomerInvoice)
	{
    $salesOrder = SalesOrder::warehouse()->where('number', '=', $salesOrderNumber)->first();
    $salesOrderProducts = $salesOrder->products;
    foreach ($temporaryStockCustomerInvoice['products'] as $product) {
      $findProductKey = array_search($product['code'], array_column($salesOrderProducts, 'code'));
      if ($findProductKey !== false) {
        if (!isset($salesOrderProducts[$findProductKey]['soldQuantity'])) {
          $salesOrderProducts[$findProductKey]['soldQuantity'] = 0;
        }
				$salesOrderProducts[$findProductKey]['soldQuantity'] += $product['quantity'];
      }
    }
		$salesOrder->products = $salesOrderProducts;
    $salesOrder->save();
		$this->updateSalesOrderStatus($salesOrder);
		$this->updateProductSoldQuantityOfPurchaseQuotation($salesOrder);
	}

	private function updateSalesOrderStatus($salesOrder)
  {
    $totalProductSalesOrder = count($salesOrder->products);
    $numberOfProductFullySold = 0;
    $numberOfProductPartialSold = 0;
    $numberOfProductZeroSold = 0;
    foreach ($salesOrder['products'] as $key => $product) {
			if (isset($product['soldQuantity'])) {
	      if ($product['soldQuantity'] === $product['quantity']) {
	        $numberOfProductFullySold  += 1;
	      } else if ($product['soldQuantity'] < $product['quantity']) {
	        $numberOfProductPartialSold += 1;
					$salesOrder->status = 'Venta parcial';
	      }
			}
    }

    if ($numberOfProductFullySold === $totalProductSalesOrder) {
      $salesOrder->status = 'Venta completa';
    } else if ($numberOfProductFullySold < $totalProductSalesOrder && $numberOfProductFullySold > 0) {
      $salesOrder->status = 'Venta parcial';
    } else if ($numberOfProductPartialSold === $totalProductSalesOrder) {
      $salesOrder->status = 'Recibido parcial';
    }

    $salesOrder->save();
  }

	private function updateProductSoldQuantityOfPurchaseQuotation($salesOrder)
	{

    $purchaseQuotation = PurchaseQuotation::warehouse()->where('number', '=', $salesOrder['purchaseQuotationNumber'])->first();
    $salesOrderProducts = $salesOrder->products;
		$purchaseQuotationProducts = $purchaseQuotation->products;
    foreach ($salesOrderProducts as $product) {
      $findProductKey = array_search($product['code'], array_column($purchaseQuotationProducts, 'code'));
      if ($findProductKey !== false) {
        if (isset($product['soldQuantity'])) {
          $purchaseQuotationProducts[$findProductKey]['soldQuantity'] = $product['soldQuantity'];
        }
      }
    }
		$purchaseQuotation->products = $purchaseQuotationProducts;
    $purchaseQuotation->save();
	}

	private function reserveStock($temporaryStockCustomerInvoice)
	{
		foreach ($temporaryStockCustomerInvoice['products'] as $key => $product) {
      HistoryStockProductReserveMaker::reserve($product, $product['quantity'], $temporaryStockCustomerInvoice['number'], 'TemporaryStockCustomerInvoice');
		}
	}

	private function updateSalesOrderHistory($salesOrderNumber, $temporaryStockCustomerInvoiceNumber)
	{
		$newHistory = ['number' => $temporaryStockCustomerInvoiceNumber, 'invoincing' => FALSE];
		$salesOrder = SalesOrder::warehouse()->where('number', '=', $salesOrderNumber)->first();
		$temporaryStockCustomerInvoiceNumberHistory = isset($salesOrder['temporaryStockCustomerInvoiceNumberHistory']) ?
			$salesOrder['temporaryStockCustomerInvoiceNumberHistory'] : [];
		array_push($temporaryStockCustomerInvoiceNumberHistory, $newHistory);
		$salesOrder->temporaryStockCustomerInvoiceNumberHistory = $temporaryStockCustomerInvoiceNumberHistory;
		$salesOrder->save();
	}

	private function getSecuencial()
  {
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '020')
      ->where('warehouseId', '=', Session::get('warehouseId'))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateCustomerInvoiceNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

}
