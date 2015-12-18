<?php namespace App\Http\Controllers;

use App\Models\DocumentConfiguration;
use App\Models\ImportQuotation;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SalesOrder;
use App\Helpers\ResultMsgMaker;
use App\Helpers\HistoryStockProductReserveMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Helpers\TemporaryKardexMaker;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for ImportQuotation Model
|--------------------------------------------------------------------------
*/

class ImportQuotationController extends Controller {

  public function index()
  {
    $importQuotations = ImportQuotation::warehouse()->get();

    return $importQuotations;
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

    $totalRecords = ImportQuotation::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = ImportQuotation::warehouse()
      ->with('customer')
      ->where(function($query) use($startDate, $endDate, $status){
        if(count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if($startDate != ''&& $endDate != '') {
          $query->whereBetween('date', [$startDate, $endDate]);
        }
      })
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

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $customers];
    return $returnData;
  }

  public function importQuotationWithSuppliersForTable()
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

    $totalRecords = ImportQuotation::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $importQuotations = ImportQuotation::warehouse()
      ->with('customer')
      ->where(function($query) use($startDate, $endDate, $status){
        if(count($status) > 0) {
          $query->whereRaw(['status' => ['$in' => $status]]);
        }
        if($startDate != ''&& $endDate != '') {
          $query->whereBetween('date', [$startDate, $endDate]);
        }
      })
      ->whereNotIn('status', ['Pedido generado'])
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $importQuotations = $importQuotations->filter(function($importQuotation) use($searchValue){
        if (stripos($importQuotation, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $importQuotations->count();
    }

    $finalImportQuotations = [];
    foreach ($importQuotations as $key => $importQuotation) {
      $newImportQuotation = $importQuotation;
      $suppliersNames = [];
      $productNames = [];
      foreach ($importQuotation['products'] as $key => $product) {
        array_push($productNames, $product['name']);
        if (isset($product['supplier_id'])) {
          $supplier = Supplier::find($product['supplier_id']);
          $businessName = isset($supplier['businessName']) ? $supplier['businessName'] : '';
          $comercialName = isset($supplier['comercialName']) ? $supplier['comercialName'] : '';
          $supplierNames = isset($supplier['names']) ? $supplier['names'] : '';
          $supplierSurnames = isset($supplier['surnames']) ? $supplier['surnames'] : '';
          $supplierCompleteName = $supplierNames.' '.$supplierSurnames;
          $finalName = '';
          $finalName = ($supplierCompleteName != '' ? $supplierCompleteName : $finalName);
          $finalName = ($comercialName != '' ? $comercialName : $finalName);
          $finalName = ($businessName != '' ? $businessName : $finalName);
          array_push($suppliersNames, $finalName);
        }
      }
      $newImportQuotation['productsNames'] = $productNames;
      $newImportQuotation['suppliers'] = $suppliersNames;
      array_push($finalImportQuotations, $newImportQuotation);
    };

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $finalImportQuotations];
    return $returnData;


  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $importQuotation = ImportQuotation::warehouse()->where($parameter['parameter'], '=', $parameter['value'])->first();
    return $importQuotation;
  }

  public function forDistribution()
  {
    $parameter = Input::all();
    $importQuotations = ImportQuotation::warehouse()->whereIn('number', $parameter['importQuotationNumbers'])
      ->whereNotNull('documentFromNumber')
      ->get();
    $importQuotations = $this->filterImportQuotationByPartialSalesOrder($importQuotations->toArray());
    $importQuotations = $this->filterImportQuotationProductsBySupplier($importQuotations, $parameter['supplier_id']);
    $importQuotations = $this->setDistributionQuantity($importQuotations);

    return $importQuotations;
  }

  private function filterImportQuotationByPartialSalesOrder($importQuotations)
  {
    $partialImportQuotations = [];
    foreach ($importQuotations as $keyImportQuotation => $importQuotation) {
      $salesOrder = SalesOrder::warehouse()->where('number', '=', $importQuotation['documentFromNumber'])->first();
      if ($salesOrder->status == 'Venta parcial' || $salesOrder->status == 'Recibido parcial' || $salesOrder->status == 'Solicitud de importación generada') {
        array_push($partialImportQuotations, $importQuotation);
      }
    }
    return $partialImportQuotations;
  }

  private function filterImportQuotationProductsBySupplier($importQuotations, $supplierId)
  {
    $filterImportQuotations = [];
    foreach ($importQuotations as $keyImportQuotation => $importQuotation) {
      $newImportQuotation = $importQuotation;
      $newImportQuotation['products'] = [];
      foreach ($importQuotation['products'] as $key => $product) {
        if ($product['supplier_id'] === $supplierId) {
          if (isset($product['distributionQuantity']) && $product['distributionQuantity'] > 0) {
            if ($product['quantity'] !== $product['distributionQuantity']) {
              array_push($newImportQuotation['products'], $product);
            }
          } else {
            array_push($newImportQuotation['products'], $product);
          }
        }
      }

      if (count($newImportQuotation['products']) > 0) {
        array_push($filterImportQuotations, $newImportQuotation);
      }
    }
    return $filterImportQuotations;
  }

  private function setDistributionQuantity($importQuotations)
  {
    foreach ($importQuotations as $keyImportQuotation => $importQuotation) {
      foreach ($importQuotation['products'] as $keyProduct => $product) {
        if (!isset($product['distributionQuantity'])){
          $importQuotations[$keyImportQuotation]['products'][$keyProduct]['distributionQuantity'] = 0;
        }
      }
    }
    return $importQuotations;
  }



  public function saveDistribution()
  {
    $importQuotations = Input::all();
    foreach ($importQuotations as $keyImportQuotation => $importQuotation) {
      $this->updateDistributedQuantityOfImportQuotation($importQuotation);
      TemporaryKardexMaker::registerOuputFromDistribution($importQuotation);
      $this->updateSalesOrderFromDistribution($importQuotation['_id']);
    }
    return ResultMsgMaker::saveSuccess();
  }

  private function updateDistributedQuantityOfImportQuotation($importQuotation)
  {
    $importQuotationProducts = $importQuotation['products'];
    $savedImportQuotation = ImportQuotation::find($importQuotation['_id']);
    $savedProducts = $savedImportQuotation->products;
    foreach ($importQuotationProducts as $importQuotationProduct) {
      $findProductKey = array_search($importQuotationProduct['code'], array_column($savedProducts, 'code'));
      if ($findProductKey !== false) {
        if (!isset($savedProducts[$findProductKey]['distributionQuantity'])) {
          $savedProducts[$findProductKey]['distributionQuantity'] = 0;
        }
        if (isset($importQuotationProduct['quantityForDistribution'])) {
          $savedProducts[$findProductKey]['distributionQuantity'] += $importQuotationProduct['quantityForDistribution'];
          $this->reserveStock($savedProducts[$findProductKey], $importQuotationProduct['quantityForDistribution'], $importQuotation);
        }
      }
    }
    $savedImportQuotation->products = $savedProducts;
    $savedImportQuotation->save();
  }


  private function updateSalesOrderFromDistribution($importQuotationId)
  {
    $importQuotation = ImportQuotation::find($importQuotationId);
    $salesOrder = SalesOrder::warehouse()->where('number', '=', $importQuotation['documentFromNumber'])->first();
    $salesOrderProducts = $salesOrder->products;
    foreach ($importQuotation->products as $product) {
      $findProductKey = array_search($product['code'], array_column($salesOrderProducts, 'code'));
      if ($findProductKey !== false) {
        if (!isset($product['distributionQuantity'])) {
          $salesOrderProducts[$findProductKey]['distributionQuantity'] = 0;
        } else {
          $salesOrderProducts[$findProductKey]['distributionQuantity'] = $product['distributionQuantity'];
        }

      }
    }
    $salesOrder->products = $salesOrderProducts;
    $salesOrder->save();
    $this->updateSalesOrderStatus($salesOrder);
  }

  private function updateSalesOrderStatus($salesOrder)
  {
    $totalProductSalesOrder = count($salesOrder->products);
    $numberOfProductFullyDistribution = 0;
    $numberOfProductPartialDistribution = 0;
    $numberOfProductZeroDistribution = 0;
    foreach ($salesOrder['products'] as $key => $product) {
      if ($product['distributionQuantity'] === $product['quantity']) {
        $numberOfProductFullyDistribution  += 1;
      } else if ($product['distributionQuantity'] < $product['quantity']) {
        $numberOfProductPartialDistribution += 1;
      } else if ($product['distributionQuantity'] == 0) {
        $numberOfProductZeroDistribution += 0;
      }
    }

    if ($numberOfProductFullyDistribution === $totalProductSalesOrder) {
      $salesOrder->status = 'Recibido completo';
    } else if ($numberOfProductFullyDistribution < $totalProductSalesOrder && $numberOfProductFullyDistribution > 0) {
      $salesOrder->status = 'Recibido parcial';
    } else if ($numberOfProductPartialDistribution === $totalProductSalesOrder) {
      $salesOrder->status = 'Recibido parcial';
    }

    $salesOrder->save();
  }

  private function reserveStock($product, $quantity, $salesOrder)
  {
    HistoryStockProductReserveMaker::reserve($product, $quantity, $salesOrder['number'], 'GoodsDistribution');
  }



  private function getProductsOfImportQuotation($importQuotations, $supplierId)
  {
    $products = [];
    foreach ($importQuotations as $key => $importQuotation) {
      $importQuotationProducts = [];
      foreach ($importQuotation['products'] as $key => $product) {
        $quantityRemaining = isset($product['quantityRemaining']) ? $product['quantityRemaining'] : $product['quantity'];
        if($quantityRemaining != 0){
          if (isset($product['supplier_id']) && $product['supplier_id'] === $supplierId) {
            $selectedProduct = $product;
            $selectedProduct['importQuotationNumber'] = $importQuotation['number'];
            $selectedProduct['customer'] = Customer::find($importQuotation['customer_id'])->toArray();
            $selectedProduct['supplier'] = Supplier::find($product['supplier_id'])->get()->toArray();
            $selectedProduct['selectedQuantity'] = $quantityRemaining;
            array_push($importQuotationProducts, $selectedProduct);
          }
        }
      }
      $products = array_merge($products, $importQuotationProducts);
    }
    return $products;
  }

  public function getAllByParameterPostForConsolidation()
  {
    $parameter = Input::all();
    $importQuotations = ImportQuotation::warehouse()->where($parameter['parameter'], '=', $parameter['value'])
      ->whereIn('status', ['Abierto', 'Pedido parcial'])
      ->get();
    $products = $this->getProductsOfImportQuotation($importQuotations, $parameter['value']);
    return $products;
  }

  public function getImportQuotationSuppliers()
  {
    $supplier_ids = [];
    $importQuotations = ImportQuotation::warehouse()->get();
    foreach ($importQuotations as $key => $importQuotation) {
      foreach ($importQuotation['products'] as $key => $product) {
        if (isset($product['supplier_id'])) {
          array_push($supplier_ids, $product['supplier_id']);
        }
      }
    }

    $supplier_ids = array_unique($supplier_ids);
    $suppliers = Supplier::whereIn('_id', $supplier_ids)->get();
    return $suppliers;
  }


  public function store()
  {
    $newImportQuotation = Input::all();
    $newImportQuotation['number'] = $this->generateImportQuotationNumber();
    if(ImportQuotation::create($newImportQuotation)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  public function show()
  {
    $newImportQuotation = Input::get();
    $newImportQuotation['number'] = $this->generateImportQuotationNumber();
    if(ImportQuotation::create($newImportQuotation)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->updateSalesOrder($newImportQuotation);
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateSalesOrder($importQuotation)
  {
    $salesOrder = SalesOrder::warehouse()->where('number', '=', $importQuotation['documentFromNumber'])->first();
    $salesOrder->status = 'Solicitud de importación generada';
    $salesOrder->modelToName = 'ImportQuotation';
    $salesOrder->documentToName = 'importQuotation';
    $salesOrder->documentToNumber = $importQuotation['number'];
    $salesOrder->save();

  }

  public function update($id)
  {
    $importQuotation = ImportQuotation::find($id);
    $newData = Input::all();
    if($importQuotation->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $importQuotation = ImportQuotation::find($id);
    $annulDate = new \DateTime();
    $importQuotation->annulDate = $annulDate->format('Y-m-d H:i:s');
    $importQuotation->status = 'Anulado';
    if ($importQuotation->save()) {
      if ($importQuotation->documentFromNumber != null) {
        $this->revertSalesOrder($importQuotation->documentFromNumber);
      }
      return ResultMsgMaker::annulSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function revertSalesOrder($documentNumber)
  {
    $document = SalesOrder::warehouse()->where('number', '=', $documentNumber)->first();
    $document->status = 'Abierto';
    $document->documentToName = null;
    $document->documentToNumber = null;
    $document->save();
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '022')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateImportQuotationNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
