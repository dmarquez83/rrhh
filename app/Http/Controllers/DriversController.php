<?php namespace App\Http\Controllers;

use App\Models\Driver;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Driver Model
|--------------------------------------------------------------------------
*/

class DriversController extends Controller {


  public function index()
  {
    $drivers = Driver::all();
    return $drivers;
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

    $totalRecords = Driver::count();
    $recordsFiltered = $totalRecords;
    $drivers = Driver::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $drivers = $drivers->map(function($driver){
      $newDriver = $driver;

      return $newDriver;
    });

    if($searchValue!=''){
      $drivers = $drivers->filter(function($driver) use($searchValue){
        if (stripos($driver, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $drivers->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $drivers);
    return $returnData;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $driver = Driver::where($parameter['parameter'], '=', $parameter['value'])->first();
    return $driver;
  }

  public function store()
  {
    $driver = Input::all();
    if(Driver::create($driver)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $driver = Input::all();
    $savedDriver = Driver::find($id);
    if($savedDriver->update($driver)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = true;
    if($canRemove === true){
      if (Driver::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'transportista', '', $modelName);
    }
  }

}