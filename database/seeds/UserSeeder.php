<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder {

    public function run() {
        DB::table('Users')->delete();
        $currentCompany =  Session::get('createdDataBase');
        if (isset($currentCompany['databaseName']) && $currentCompany['databaseName'] == 'sae') {
          $this->createUser();
        }
        if($currentCompany == null) {
          $this->createUser();
        }
    }

    public function createUser(){
      User::create([
        'username'           => 'root',
        'password'           => Hash::make('123456'),
        'role_id'            => '54e15ee2b144fddc0d123456',
        'default_company_id' => '558610c4d4c6ff7f58282e9d',
        'isNew'              => TRUE,
        'companies'          => [
          '558610c4d4c6ff7f58282e9d'
        ],
        'configuration'      => [
          [
            'company_id'           => '558610c4d4c6ff7f58282e9d',
            'role_id'              => '54e15ee2b144fddc0d123456',
            'employe_id'           => '',
            'default_warehouse_id' => '54e1a733b144fd6c0e000029',
            'selected_warehouses'  => [
              '54e1a733b144fd6c0e000029'
            ]
          ]
        ],
        'isEnabled'          => TRUE
      ]);

    }

}
