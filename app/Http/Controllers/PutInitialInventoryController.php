<?php namespace App\Http\Controllers;

use App\Models\PutInitialInventory;
use App\Helpers\ResultMsgMaker;
use App\Helpers\KardexMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;


/*
|--------------------------------------------------------------------------
| DONT USE WAREHOUSE for TariffsHeading Model
|--------------------------------------------------------------------------
*/

class PutInitialInventoryController extends Controller {

  public function index()
  {
  }

  public function store()
  {
    $putInitialInventory = Input::all();
    $this->registerProductsInput($putInitialInventory['products']);
    if(PutInitialInventory::create($putInitialInventory)){
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function registerProductsInput($products)
  {
    $concept = "Para registrar ingreso de inventario inicial";
    foreach($products as $product){
      $concept = "Para registrar ingreso de inventario inicial";
      $product['price'] = $product['unitPrice'];
      KardexMaker::registerCleanInput($product, $product['quantity'], '', $concept, 'InitialInventory');
    }
  }

}
