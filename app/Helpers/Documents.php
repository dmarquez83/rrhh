<?php namespace App\Helpers;

use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Session;

class Documents {

  private $documentConfiguration;

  public function getNumber($documentCode)
  {
    $number = str_pad($this->getSecuencial($documentCode), 9, "0", STR_PAD_LEFT);
    $companySerie = $this->documentConfiguration->companySerie;
    $warehouseSerie = $this->documentConfiguration->warehouseSerie;
    $prefix = $companySerie.'-'.$warehouseSerie.'-';

    return $prefix.$number;
  }

  private function getSecuencial($documentCode)
  {
    $currentWarehouse = Session::get('currentWarehouse');
    $this->documentConfiguration = DocumentConfiguration::where('code', '=', $documentCode)
      ->where('warehouse_id', '=', new \MongoId($currentWarehouse['_id']))->first();
    $newSecuencial = $this->documentConfiguration->secuencial + 1;
    return $newSecuencial;
  }

  public function updateSecuencial()
  {
    $this->documentConfiguration->secuencial += 1;
    $this->documentConfiguration->save();
  }
}