<?php namespace App\Http\Controllers;

use App\Models\DocumentConfiguration;
use App\Models\GoodsDelivery;
use App\Models\ReferralGuide;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for GoodsDelivery Model
|--------------------------------------------------------------------------
*/

class GoodsDeliveryController extends Controller {

 public function index()
  {
    $goodsDeliverys = GoodsDelivery::warehouse()->get();

    return $goodsDeliverys;
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

    $totalRecords = GoodsDelivery::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $customers = GoodsDelivery::warehouse()
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

  public function generateFromReferralGuide() 
  {
    $referralGuide = Input::all();
    $date = new \DateTime();
    $newGoodsDelivery = $referralGuide;
    $newGoodsDelivery['creationDate'] = $date->format('Y-m-d H:i:s');
    $newGoodsDelivery['status'] = 'Pendiente';
    $newGoodsDelivery['number'] = $this->generateGoodsDeliveryNumber();
    $newGoodsDelivery['referralGuideNumber'] = $referralGuide['number'];
    $newGoodsDelivery['products'] = $referralGuide['products'];
    if (GoodsDelivery::create($newGoodsDelivery)) {
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      $this->updateReferralGuide($referralGuide, $newGoodsDelivery['number']);
      return ResultMsgMaker::successCustom('Se creó una guía de remisión correctamente');
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function updateReferralGuide($referralGuide, $newGoodsDeliveryNumber)
  {
    $referralGuide = ReferralGuide::find($referralGuide['_id']);
    $referralGuide->goodsDeliveryNumber = $newGoodsDeliveryNumber;
    $referralGuide->status = 'Pendiente de despacho';
    $referralGuide->save();
  }


  public function store()
  {
    $newGoodsDelivery = Input::get();
    $newGoodsDelivery['number'] = $this->generateGoodsDeliveryNumber();
    $newGoodsDelivery['customer_id'] = new MongoId($newGoodsDelivery['customer_id']);
    if(GoodsDelivery::create($newGoodsDelivery)){
      $this->documentConfiguration->secuencial += 1;
      $this->documentConfiguration->save();
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  public function storeFromPurchaseCreditNote()
  {

  }

  public function update($id)
  {
    $GoodsDelivery = GoodsDelivery::find($id);
    $newData = Input::all();
    if($GoodsDelivery->update($newData)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function goodsDispatch()
  {
    $newData = Input::all();
    $goodsDelivery = GoodsDelivery::find($newData['_id']);
    $goodsDelivery->status = 'En transito';
    if($goodsDelivery->save()) {
      $referralGuide = ReferralGuide::warehouse()->where('number', '=', $goodsDelivery->referralGuideNumber)->first();
      $referralGuide->status = 'En transito';
      $referralGuide->save(); 
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    if(GoodsDelivery::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  private function getSecuencial()
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', '015')
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;

    return $newSecuencial;
  }

  private function generateGoodsDeliveryNumber()
  {
    $number = str_pad($this->getSecuencial(), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }


}
