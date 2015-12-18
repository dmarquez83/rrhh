<?php namespace App\Http\Controllers;

use App\Models\ElectronicDocument;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Driver Model
|--------------------------------------------------------------------------
*/

class ElectronicDocumentsController extends Controller {

  public function searchByParameters()
  {
    $params = Input::all();

    $document = ElectronicDocument::where(function($query) use($params){
      foreach ($params as $key => $parameter) {
        $query->where($parameter['parameter'], $parameter['value']);
      }
    })->first();
    return $document;
  }

}
