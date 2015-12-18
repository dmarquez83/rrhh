<?php

use App\Models\PriceList;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder {

    public function run() {
      DB::collection('Warehouses')->delete();

      $currentCompany =  Session::get('createdDataBase');

      if (isset($currentCompany['databaseName']) && $currentCompany['databaseName'] == 'sae') {
        Warehouse::create([
          '_id'       => '54e1a733b144fd6c0e000029',
          'series'    => '001',
          'code'      => '001',
          'name'      => 'Matriz',
          'address'   => 'La carolina',
          'telephone' => '022730427',
          'country'   => 'Ecuador',
          'city'      => 'Quito'
        ]);
      }

      if($currentCompany == null) {
        Warehouse::create([
          '_id'       => '54e1a733b144fd6c0e000029',
          'series'    => '001',
          'code'      => '001',
          'name'      => 'Matriz',
          'address'   => 'La carolina',
          'telephone' => '022730427',
          'country'   => 'Ecuador',
          'city'      => 'Quito'
        ]);
      }

    }

}
