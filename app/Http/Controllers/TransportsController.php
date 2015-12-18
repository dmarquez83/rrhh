<?php namespace App\Http\Controllers;

use App\Models\Transport;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Transport Model
|--------------------------------------------------------------------------
*/

class TransportsController extends Controller {


  public function index()
  {
    $transports = Transport::all();
    return $transports;
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

    $totalRecords = Transport::count();
    $recordsFiltered = $totalRecords;
    $transports = Transport::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $transports = $transports->map(function($transport){
      $newTransport = $transport;
      
      return $newTransport;
    });

    if($searchValue!=''){
      $transports = $transports->filter(function($transport) use($searchValue){
        if (stripos($transport, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $transports->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $transports);
    return $returnData;
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $transport = Transport::where($parameter['parameter'], '=', $parameter['value'])->first();
    return $transport;
  }

  public function store()
  {
    $transport = Input::all();
    if(Transport::create($transport)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $transport = Input::all();
    $savedTransport = Transport::find($id);
    if($savedTransport->update($transport)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = true;
    if($canRemove === true){
      if (Transport::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'transporte', '', $modelName);
    }
  }

}
