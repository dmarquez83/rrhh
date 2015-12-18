<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for DocumentConfiguration Model
|--------------------------------------------------------------------------
*/

class DocumentsConfigurationController extends Controller {


  public function index()
  {
    $documentsConfiguration = DocumentConfiguration::warehouse()->get();
    return $documentsConfiguration;
  }

  public function contable()
  {
    $documentsConfiguration = DocumentConfiguration::warehouse()
      ->where('isContable', '=', true)
      ->get();
    return $documentsConfiguration; 
  }

  public function store()
  {
    $document = Input::all();
    
    if(DocumentConfiguration::create($document)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    };
  }

}
