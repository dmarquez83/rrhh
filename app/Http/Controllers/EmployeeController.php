<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\DocumentReferenceVerificator;
use App\Models\Department;
use App\Models\Driver;
use App\Models\Employee;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Gregwar\Image\Image as ImageGregwar;

/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for Employee Model
|--------------------------------------------------------------------------
*/

class EmployeeController extends Controller {


  public function index()
  {
    $employees = Employee::with('department', 'office', 'maritalStatus','bank')->get();
    return $employees;
  }

  public function basicInfo() {
      return DB::table('Employees')->select('_id', 'identification', 'code',
                                            'photo', 'names', 'surnames',
                                            'email')->get();
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

    $totalRecords = Employee::count();
    $recordsFiltered = Employee::count();
    $employees = Employee::skip($start)
      ->with('department', 'office', 'maritalStatus','bank')
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    if($searchValue!=''){
      $employees = $employees->filter(function($customer) use($searchValue){
        if (stripos($customer, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $employees->count();
    }

    $returnData = [
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $employees];
    return $returnData;
  }


  public function store()
  {
    $newEmployee = Input::all();

    $absolutePath = public_path().'/images/employeesPhotos/'.$newEmployee['identification'].'.jpg';
    $realtivePath = '/images/employeesPhotos/'.$newEmployee['identification'].'.jpg';

    if (isset($newEmployee['photo'])){
      $photo = $newEmployee['photo']['src'];
      $photo = str_replace('data:image/jpeg;base64,','',$photo);
      $photo = base64_decode($photo);
      $fp = fopen($absolutePath, 'w');
      fwrite($fp, $photo);
      fclose($fp);
      ImageGregwar::open($absolutePath)
      ->resize(170, 180)
      ->save($absolutePath);
        $newEmployee['photo'] = ['src' => $realtivePath];
    } else {
      $newEmployee['photo'] = ['src'=> $realtivePath];
    }

    $newEmployee['salaryHistory'] = [[
      'date' => date("Y-m-d H:i:s"),
      'salary' => $newEmployee['grossSalary'],
      'observation' => 'Salario Inicial']];

    $newEmployee['departmentHistory'] = [[
      'date' => date("Y-m-d H:i:s"),
      'department' => [Department::find($newEmployee['department_id'])->toArray()],
      'observation' => 'Departamento Inicial']];

    $newEmployee['discounts'] = [];

    if (Employee::create($newEmployee)) {
      if ($newEmployee['isDriver']) {
        $this->createDriver($newEmployee);
      }
      return ResultMsgMaker::saveSuccess();
    }
    return ResultMsgMaker::error();
  }

  private function createDriver($employee)
  {
    $newDriver = [];
    $newDriver['identification'] =  $employee['identification'];
    $newDriver['name'] =  $employee['names'].' '.$employee['surnames'];
    $savedDriver = Driver::where('identification', '=', $newDriver['identification'])->first();
    if($savedDriver){
      $savedDriver->update($newDriver);
    } else {
      Driver::create($newDriver);
    }
  }

  public function update($id)
  {
    $savedEmployee = Employee::find($id);
    $newData = Input::all();
    if($savedEmployee->update($newData)){
      if ($savedEmployee['isDriver'] === true) {
        $this->createDriver($savedEmployee);
      }
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $modelList = ['Department', 'Office', 'Bonus', 'Discount'];
    $canRemove = DocumentReferenceVerificator::verify("employee_id", $id, $modelList);

    if($canRemove === true){
      if (Employee::where('_id', '=', $id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);
      return ResultMsgMaker::errorCannotDelete('el', 'empleado', '', $modelName);
    }
  }

  public function getEmployees() {

    $Employees = Employee::with('department', 'office', 'maritalStatus','bank')->get();

    return $Employees;
  }

}
