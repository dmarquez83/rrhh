<?php namespace App\Http\Controllers;

use App\Models\TariffsHeading;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for TariffsHeading Model
|--------------------------------------------------------------------------
*/

class TariffHeadingsController extends Controller {


  public function index()
  {
    $tariffHeadings = TariffsHeading::where('type', '=', 3)->get();
    $tariffHeadingsWithParent = $tariffHeadings->map(function($tariffHeading){
      $newTariffHeading = $tariffHeading;
      if($tariffHeading->parentTariffHeading_id){
        $newTariffHeading->parent = TariffsHeading::find($tariffHeading->parentTariffHeading_id)->toArray();
      }
      return $newTariffHeading;
    });
    return $tariffHeadingsWithParent;
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

    $totalRecords = TariffsHeading::count();
    $recordsFiltered = $totalRecords;
    $tariffHeadings = TariffsHeading::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $tariffHeadings = $tariffHeadings->map(function($tariffHeading){
      $newTariffHeading = $tariffHeading;
      if($tariffHeading->parentTariffHeading_id){
        $newTariffHeading->parent = TariffsHeading::find($tariffHeading->parentTariffHeading_id)->toArray();
      }
      return $newTariffHeading;
    });

    if($searchValue!=''){
      $tariffHeadings = $tariffHeadings->filter(function($tariffHeading) use($searchValue){
        if (stripos($tariffHeading, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $tariffHeadings->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $tariffHeadings);
    return $returnData;
  }

  public function store()
  {
    $tariffsHeading = Input::all();
    if(TariffsHeading::create($tariffsHeading)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $tariffsHeading = Input::all();
    $savedTariffsHeading = TariffsHeading::find($id);
    if($savedTariffsHeading->update($tariffsHeading)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }

  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify(['tariffHeading_id', 'parentTariffHeading_id'], $id, ['Product', 'TariffsHeading']);

    if($canRemove === true){
      if (TariffsHeading::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'partida arancelaria', '', $modelName);
    }
  }

}
