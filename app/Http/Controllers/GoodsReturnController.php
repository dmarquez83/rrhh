<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\DocumentConfiguration;
use App\Models\GoodsReturn;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for GoodsReturn Model
|--------------------------------------------------------------------------
*/

class GoodsReturnController extends Controller {

  public function index()
  {
    $goodsReturn = GoodsReturn::warehouse()->get();

    return $goodsReturn;
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

    $searchValue = $params['search']['value'];

    $totalRecords = GoodsReturn::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = GoodsReturn::warehouse()
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

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $customers);
    return $returnData;
  }

  public function store()
  {
    $goodsReturn = Input::get();
    $goodsReturn['number'] = $this->generateGoodsReturnNumber();
    if(GoodsReturn::create($goodsReturn)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();

      GeneralJournalMaker::generateJournalEntry($goodsReturn, '011');

      foreach($goodsReturn['products'] as $product) {
        KardexMaker::registerOutput($product, $goodsReturn['number'], 'GoodsReturn');
      } 

      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    if(GoodsReturn::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '011')
      ->where('warehouse_id', '=', new MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;              

    return $newSecuencial;             
  }

  private function generateGoodsReturnNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
