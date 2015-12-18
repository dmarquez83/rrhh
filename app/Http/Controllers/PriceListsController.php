<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\PricesList;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for PricesList Model
|--------------------------------------------------------------------------
*/

class PriceListsController extends Controller {

	public function index()
	{
		$priceLists = PricesList::warehouse()->get();
		return $priceLists;
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

    $totalRecords = PricesList::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $priceLists = PricesList::warehouse()
      ->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $priceLists = $priceLists->map(function($priceList){
    $newPriceList = $priceList;

      return $newPriceList;
    });

    if($searchValue!=''){
      $priceLists = $priceLists->filter(function($priceList) use($searchValue){
        if (stripos($priceList, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $priceLists->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $priceLists);
    return $returnData;
  }

  public function store()
  {
    $pricesList = Input::all();
    if(PricesList::create($pricesList)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $pricesList = Input::all();
    $savedPricesList = PricesList::find($id);
    if($savedPricesList->update($pricesList)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify(['prices_list_ids'], $id, ['Product']);
    if($canRemove === true){
      if (PricesList::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'lista de precios', '', $modelName);
    }
  }

}
