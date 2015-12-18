<?php namespace App\Http\Controllers;

use App\Models\InventoryRemoval;
use App\Helpers\ResultMsgMaker;
use App\Helpers\KardexMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for TariffsHeading Model
|--------------------------------------------------------------------------
*/

class InventoryRemovalController extends Controller {

  public function index()
  {
  }

  public function store()
  {
    $inventoryRemoval = Input::all();
    $this->registerProductsOutput($inventoryRemoval['products']);
    if(InventoryRemoval::create($inventoryRemoval)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function registerProductsOutput($products)
  {
    $concept = "Para registrar ingreso de inventario inicial";
    foreach($products as $product){
      $concept = "Para registrar ingreso de inventario inicial";
      $product['price'] = $product['unitCost'];
      KardexMaker::registerCleanOutput($product, $product['quantity'], '', $concept, 'InvetoryRemoval');
    }
  }

}
