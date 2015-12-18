<?php namespace App\Http\Controllers;

use App\Helpers\ResultMsgMaker;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller {

    public function getWarehouses() {
      $userInformation = Session::get('userInformation');
      return $userInformation['warehouses'];
    }

    public function getCurrentWarehouse() {
        return Session::get('currentWarehouse');
    }

    public function setSelectedWarehouse() {
        $selectedWarehouse = Input::get('warehouse');

        if(!empty($selectedWarehouse)){
            Session::put('currentWarehouse', $selectedWarehouse);
        }
    }


    public function getCompanies() {
      $userInformation = Session::get('userInformation');
      $configurationCompanies = $userInformation['systemUser']['configuration'];
      $companies = [];
      foreach($configurationCompanies as $configurationCompany){
        $company = Company::find($configurationCompany['company_id']);
        if ($userInformation['systemUser']['username'] != 'root') {
          if ($company->databaseName != 'sae') {
            $companies[] = $company->toArray();
          }
        } else {
          $companies[] = $company->toArray();
        }
      }
      return $companies;
    }

    public function changeCompany() {

      $defaultCompany = Input::all();

      $userInformation = Session::get('userInformation');
      $user = $userInformation['systemUser'];
      Session::put('currentCompany', $defaultCompany);

      if($defaultCompany['databaseName'] != 'sae'){
        Config::set('database.connections.'.$defaultCompany['databaseName'], [
          'driver' => 'mongodb',
          'host' => 'localhost',
          'port' => 27017,
          'username' => '',
          'password' => '',
          'database' => $defaultCompany['databaseName']
        ]);


      }

      Config::set('database.default', $defaultCompany['databaseName']);

      $userRolId = '';
      $selectedWarehouses = [];
      $currentWarehouseId = '';
      $warehouses = [];
      foreach($user['configuration'] as $companyConfiguration){
        if ($companyConfiguration['company_id'] == $defaultCompany['_id']){
          $selectedWarehouses = $companyConfiguration['selected_warehouses'];
          $currentWarehouseId = $companyConfiguration['default_warehouse_id'];
          $userRolId = $companyConfiguration['role_id'];
        }
      }

      foreach ($selectedWarehouses as $warehouseId){
        $warehouseInformation = Warehouse::find($warehouseId);
        $warehouses[] = $warehouseInformation->toArray();
      }

      $currentWarehouse = Warehouse::find($currentWarehouseId);
      Session::put('currentWarehouse', $currentWarehouse);

      $userRolId = $userRolId == '' ? $user['role_id'] : $userRolId;
      $role = Role::find($userRolId);
      $modules = [];

      $employeeInformation = [];


      foreach ($role->modules as $module){
        $moduleInformation = Module::find($module['_id']);
        $moduleInformation = $moduleInformation->toArray();
        $moduleInformation['isSelected'] = $module['isSelected'];
        foreach($module['submodules'] as $index => $submodule){
          $moduleInformation['submodules'][$index]['isSelected'] = $submodule['isSelected'];
        }
        $modules[] = $moduleInformation;
      }


      if(isset($user['employee'])){
        $employeeInformation = Employee::find($user['employee'])
          ->select('_id', 'identification', 'code',
            'photo', 'names', 'surnames',
            'email');
      }

      $userInfo = [];
      $userInfo['systemUser'] = $user;
      $userInfo['role'] = $role->toArray();
      $userInfo['modules'] = $modules;
      $userInfo['warehouses'] = $warehouses;
      $userInfo['employee'] = $employeeInformation;


      Session::put('userInformation', $userInfo);

      $companyInfo = CompanyInfo::first();
      Session::put('companyInformation', $companyInfo);

      return $companyInfo;

    }

    public function index() {
        $currentCompany = Session::get('currentCompany');
        $finalUsers = [];
        if($currentCompany['databaseName'] != 'sae') {
          $userOfCurrentDatabase = User::where('configuration.company_id', '=', $currentCompany['_id'])->get();

          foreach ($userOfCurrentDatabase as $user) {
            $newUser = $user->toArray();
            $newUser['configuration'] = [];
            foreach ($user->configuration as $companyConfiguration) {
              if ($companyConfiguration['company_id'] == $currentCompany['_id']) {
                $newUser['configuration'][] = $companyConfiguration;
              }
            }
            $finalUsers[] = $newUser;
          }
        } else {
          $finalUsers =  User::where('configuration.company_id', '=', $currentCompany['_id'])->get();
        }

        return $finalUsers;
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

      $totalRecords = User::count();
      $recordsFiltered = $totalRecords;

      $users = User::skip($start)
        ->take($length)
        ->orderBy($columOrderName, $columOrderDir)
        ->get();

      $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $recordsFiltered,
        'data' => $users);

      return $returnData;
    }

    public function store() {
        $user = Input::all();
        $user['password'] = Hash::make($user['password']);
        $userExists = User::where('username', '=', $user['username'])->count() > 0;

        if($userExists) {
            return ResultMsgMaker::warningDuplicateField('El','usuario',$user['username']);
        }else{
            if(!User::create($user)) {
                return ResultMsgMaker::error();
            }
            else{
                return ResultMsgMaker::saveSuccess();
            }
        }
    }


    public function update($id) {
        $user = Input::all();

        $currentCompany = Session::get('currentCompany');
        if($currentCompany['databaseName'] != 'sae') {
          $savedUser = User::find($id);
          $configurationPresave = [];
          foreach ($savedUser->configuration as $savedConfiguration) {
            if ($savedConfiguration['company_id'] == $user['configuration'][0]['company_id']) {
              $configurationPresave[] = $user['configuration'][0];
            } else {
              $configurationPresave[] = $savedConfiguration;
            }
          }

          $user['configuration'] = $configurationPresave;
        }


        if(isset($user['password'])){
            $user['password'] = Hash::make($user['password']);
        }

        if (!User::find($id)->update($user)) {
            return ResultMsgMaker::error();
        }
        else{
            return ResultMsgMaker::saveSuccess();
        }

    }


    public function destroy($id) {
        if($user = User::find($id)->delete()){
          return ResultMsgMaker::deleteSuccess();
        } else {
          return ResultMsgMaker::error();
        }
    }

}
