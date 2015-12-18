<?php namespace App\Http\Controllers;

use App\Helpers\TributationFormMaker;
use App\Models\TributationForm;
use App\Models\ConfigurationTributationForm;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;
/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for PaymentMethod Model
|--------------------------------------------------------------------------
*/


class TributationFormsController extends Controller {


  public function generate()
  {
    $filters = Input::all();

    $savedTributationForm = TributationForm::where('month', '=', $filters['month'])
      ->where('type', '=', $filters['formName'])
      ->where('year', '=', $filters['year'])
      ->first();

    if ($savedTributationForm) {
      return ResultMsgMaker::warningCustom('Ya se ha generado un archivo para este periodo');
    } else {
      $tributationFormConfig = ConfigurationTributationForm::where('name', '=', $filters['formName'])->first();
      $form = TributationFormMaker::generate($filters, $tributationFormConfig->_id, $filters['formName']);
      return $form;
    }
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

    $totalRecords = TributationForm::count();
    $recordsFiltered = $totalRecords;

    $forms = TributationForm::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $forms);
    return $returnData;
  }

  public function store()
  {
    $data = Input::all();
    $filters = $data['filters'];
    $form = $data['form'];
    $tributationFormConfig = ConfigurationTributationForm::where('name', '=', $filters['formName'])->first();
    $tributationForm = [];
    $tributationForm['form'] = $form;
    $tributationForm['type'] = $filters['formName'];
    $tributationForm['month'] = $filters['month'];
    $tributationForm['year'] = $filters['year'];
    $tributationForm['status'] = 'Pendiente de confirmación';
    $tributationForm['bankAccount'] = isset($data['bankAccount']) ? $data['bankAccount'] : null;
    $fileData = TributationFormMaker::createXMLFile($form, $filters['year'], $filters['month'], $tributationFormConfig->_id, $filters['formName']);
    $tributationForm['fileName'] = $fileData['fileName'];

    if (TributationForm::create($tributationForm)) {
      return ResultMsgMaker::saveSuccessWithExtraData($fileData);
    }
    return ResultMsgMaker::error();
  }


  public  function destroy($id)
  {
    if(TributationForm::find($id)->delete()) {
      return ResultMsgMaker::deleteSuccess();
    };
    return ResultMsgMaker::errorCannotDelete();
  }

}
