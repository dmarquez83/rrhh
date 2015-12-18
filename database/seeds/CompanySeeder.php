<?php

use App\Models\Company;
use App\Models\PricesList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder  {

  public function run() {
    DB::collection('Companies')->delete();
    $currentCompany =  Session::get('createdDataBase');

    if (isset($currentCompany['databaseName']) && $currentCompany['databaseName'] == 'sae') {
      Company::create([
        "_id"          => "558610c4d4c6ff7f58282e9d",
        "isNew"        => true,
        "code"         => "0001",
        "name"         => "SAE",
        "databaseName" => "sae",
        "companyUrl"   => "http://localhost:8000/",
      ]);
    }

    if($currentCompany == null) {
      Company::create([
        "_id"          => "558610c4d4c6ff7f58282e9d",
        "isNew"        => true,
        "code"         => "0001",
        "name"         => "SAE",
        "databaseName" => "sae",
        "companyUrl"   => "http://localhost:8000/",
      ]);
      PricesList::create([
        "name" => "Lista Principal",
        "factor" => 1.0,
        'factorType' => 'multiply',
        "description" => "Lista de precios creada por el sistema"
      ]);
    }

  }

}
