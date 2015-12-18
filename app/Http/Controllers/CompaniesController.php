<?php namespace App\Http\Controllers;

use App\Helpers\ResultMsgMaker;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Module;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use MongoClient;

/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for Company Model
|--------------------------------------------------------------------------
*/

class CompaniesController extends Controller {

	public function index()
	{
    $companies  = Company::all();
    $companies = $companies->each(function($company){
      $company->warehouses = Warehouse::where('company_id', '=', $company->_id)->get(["_id", "name"])->toArray();
    });

    return $companies;
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

    $totalRecords = Company::count();
    $recordsFiltered = $totalRecords;

    $companies = Company::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $companies);

    return $returnData;
  }

	public function store()
	{
    $company = Input::all();
    if(Company::create($company)){

      define('STDIN',fopen("php://stdin","r"));

      $mongoConnection = new MongoClient();
      $newDatabase = $mongoConnection->selectDB($company['databaseName']);
      $newDatabase->createCollection('migrations');

      Config::set('database.connections.'.$company['databaseName'], [
        'driver' => 'mongodb',
        'host' => 'localhost',
        'port' => 27017,
        'username' => '',
        'password' => '',
        'database' => $company['databaseName']
      ]);

      Config::set('database.default', $company['databaseName']);
      Session::put('createdDataBase', $company['databaseName']);

      Artisan::call('migrate:refresh', [
        '--force'=> 'true',
        '--seed'=> 'true',
      ]);

      Config::set('database.default', 'sae');

      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }

	}


	public function update($id)
	{
    $company = Input::all();
    $savedCompany = Company::find($id);
    if($savedCompany->update($company)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }

	}


	public function destroy($id)
	{
    $users = User::where('company_id', '=', $id)->get();
    if ($users->count() == 0) {
      if (Company::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      return ResultMsgMaker::errorCannotDelete('la', 'empresa', '', 'Usuarios');
    }
	}

}
