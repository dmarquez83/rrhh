<?php

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder{

  public function run(){
    DB::collection('Bank')->delete();

    Bank::create(array(
      'code' => '2', 
      'name' => 'Banco de Fomento'
    ));

    Bank::create(array(
      'code' => '10', 
      'name' => 'Banco del Pichincha'
    ));

    Bank::create(array(
      'code' => '17', 
      'name' => 'Banco de Guayaquil'
    ));

    Bank::create(array(
      'code' => '19', 
      'name' => 'Banco Territorial'
    ));

    Bank::create(array(
      'code' => '25', 
      'name' => 'Banco Machala'
    ));

    Bank::create(array(
      'code' => '26', 
      'name' => 'Banco Unibanco'
    ));

    Bank::create(array(
      'code' => '29', 
      'name' => 'Banco de Loja'
    ));

    Bank::create(array(
      'code' => '30', 
      'name' => 'Banco del Pacífico'
    ));

    Bank::create(array(
      'code' => '32', 
      'name' => 'Banco Internacional'
    ));

    Bank::create(array(
      'code' => '34', 
      'name' => 'Banco Amazonas'
    ));

    Bank::create(array(
      'code' => '35', 
      'name' => 'Banco del Austro'
    ));

    Bank::create(array(
      'code' => '36', 
      'name' => 'Banco de la Producción'
    ));

    Bank::create(array(
      'code' => '37', 
      'name' => 'Banco Bolivariano'
    ));

    Bank::create(array(
      'code' => '39', 
      'name' => 'Banco Comercial de Manabi'
    ));

    Bank::create(array(
      'code' => '40', 
      'name' => 'Banco Proamerica'
    ));

    Bank::create(array(
      'code' => '42', 
      'name' => 'Banco General Rumiñahui'
    ));

    Bank::create(array(
      'code' => '43', 
      'name' => 'Banco del Litoral'
    ));

    Bank::create(array(
      'code' => '45', 
      'name' => 'Banco Sudamericano'
    ));

    Bank::create(array(
      'code' => '59', 
      'name' => 'Banco Solidario'
    ));

    Bank::create(array(
      'code' => '206', 
      'name' => 'Coop Ahorro y Crédito 29 de Octubre'
    ));

    Bank::create(array(
      'code' => '207', 
      'name' => 'Coop Ahorro y Crédito Andalucia'
    ));

    Bank::create(array(
      'code' => '210', 
      'name' => 'Coop Ahorro y Crédito El Sagrario'
    ));

    Bank::create(array(
      'code' => '219', 
      'name' => 'Coop Ahorro y Crédito Riobamba'
    ));

    Bank::create(array(
      'code' => '233', 
      'name' => 'Mutualista Ambato'
    ));

    Bank::create(array(
      'code' => '234', 
      'name' => 'Mutualista Azuay'
    ));

    Bank::create(array(
      'code' => '238', 
      'name' => 'Mutualista Pichincha'
    ));
  }
}