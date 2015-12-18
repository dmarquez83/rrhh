<?php namespace App\Http\Controllers;

use App\Models\ConfigurationTributationForm;
use App\Helpers\ResultMsgMaker;
use App\Helpers\SystemConfiguration;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for ConfigurationTributationForm Model
|--------------------------------------------------------------------------
*/


class ConfigurationTributationFormsController extends Controller {


  public function index()
  {
    $configurationTributationForms = ConfigurationTributationForm::all();
    return $configurationTributationForms;
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

    $totalRecords = ConfigurationTributationForm::count();
    $recordsFiltered = $totalRecords;
    $configurationTributationForms = ConfigurationTributationForm::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();


    if($searchValue!=''){
      $configurationTributationForms = $configurationTributationForms->filter(function($configurationTirbutationForm) use($searchValue){
        if (stripos($configurationTirbutationForm, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $configurationTributationForms->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $configurationTributationForms);
    return $returnData;
  }

  public function store()
  {
    $configurationTributationForm = Input::all();
    $result = $this->saveXMLfile($configurationTributationForm['name'], $configurationTributationForm['file']);
    $configurationTributationForm['camps'] = $result['camps'];
    $configurationTributationForm['absoluteUrl'] = $result['absoluteUrl'];
    $configurationTributationForm['fileName'] = $result['fileName'];
    unset($configurationTributationForm['file']);
    if(ConfigurationTributationForm::create($configurationTributationForm)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  private function saveXMLfile($fileName, $file)
  {
    $relativePath  = 'storage/app/baseTributationDocuments/'.$fileName.'.xml';
    $folderPath =  app_path();
    $fullPath = $folderPath.$fileName.'.xml';

    $xmlString = base64_decode(str_replace('data:text/xml;base64,', '', $file));
    $fp = fopen($fullPath, 'w');
    file_put_contents($fullPath, $xmlString);
    fclose($fp);

    $xmlparser = xml_parser_create();

    $campsArray = [];
    xml_parse_into_struct($xmlparser, $xmlString, $campsArray);
    xml_parser_free($xmlparser);

    return ['camps' => $campsArray, 'absoluteUrl' => $relativePath, 'fileName' => $fileName.'.xml'];
  }

  public function update($id)
  {
    $condition = Input::all();
    $savedcondition = ConfigurationTributationForm::find($id);
    if($savedcondition->update($condition)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    //$modelList = ['ConfigurationTributationForm'];
    //$canRemove = DocumentReferenceVerificator::verify(['paymentMethod_id'], $id, $modelList);
    $canRemove = true;
    if($canRemove === true){
      if (ConfigurationTributationForm::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'configuración', '', $modelName);
    }
  }
}
