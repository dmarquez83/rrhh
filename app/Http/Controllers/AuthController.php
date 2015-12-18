<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Role;
use App\Models\Warehouse;
use App\Models\CompanyInfo;
use App\Models\GeneralParameter;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function postLogin(Request $request)
    {

        $credentials = $request->only('username', 'password');

        if ($this->auth->attempt($credentials, $request->has('remember')))
        {
            $this->getUserInformation();
            $this->getCompanyInfo();
            return redirect()->intended($this->redirectPath())->with('profile', Auth::user());
        }

        return redirect('/')->with('flash_notice', 'Usuario o contraseña inválidos');
    }

    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath'))
        {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/dashboard';
    }

    private function getUserInformation()
    {
        $user = Auth::user();
        $defaultCompany = Company::find($user->default_company_id);
        Session::put('currentCompany', $defaultCompany->toArray());

        if($defaultCompany['databaseName'] != 'sae'){
          Config::set('database.connections.'.$defaultCompany['databaseName'], [
            'driver' => 'mongodb',
            'host' => 'localhost',
            'port' => 27017,
            'username' => '',
            'password' => '',
            'database' => $defaultCompany['databaseName']
          ]);

          Config::set('database.default', $defaultCompany['databaseName']);
        }

        $userRolId = '';
        $selectedWarehouses = [];
        $currentWarehouseId = '';
        $employeeId = '';
        $warehouses = [];
        foreach($user->configuration as $companyConfiguration){
          if ($companyConfiguration['company_id'] == $defaultCompany['_id']){
            $selectedWarehouses = $companyConfiguration['selected_warehouses'];
            $currentWarehouseId = $companyConfiguration['default_warehouse_id'];
            $userRolId = $companyConfiguration['role_id'];
            if (isset($companyConfiguration['employee_id'])){
              $employeeId = $companyConfiguration['employee_id'];
            }
          }
        }

        foreach ($selectedWarehouses as $warehouseId){
          $warehouseInformation = Warehouse::find($warehouseId);
          $warehouses[] = $warehouseInformation->toArray();
        }

        $currentWarehouse = Warehouse::find($currentWarehouseId);
        Session::put('currentWarehouse', $currentWarehouse);

        $userRolId = $userRolId == '' ? $user->role_id : $userRolId;
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


        $employeeInformation = '';
        if($employeeId != ''){
            $employeeInformation = Employee::find($employeeId);
            $employeeInformation = $employeeInformation->toArray();
        }

        $userInfo['systemUser'] = $user->toArray();
        $userInfo['role'] = $role->toArray();
        $userInfo['modules'] = $modules;
        $userInfo['warehouses'] = $warehouses;
        $userInfo['employee'] = $employeeInformation;


        Session::put('userInformation', $userInfo);

    }

    private function getCompanyInfo(){
      $companyInfo = CompanyInfo::first();
      $saeBasic = GeneralParameter::where('code', '=', 'SaeBasic')->first();
      Session::put('companyInformation', $companyInfo ? $companyInfo : null);
      Session::put('saeBasic', $saeBasic->alfanumericValue == '1' ? true : false);
    }

}
