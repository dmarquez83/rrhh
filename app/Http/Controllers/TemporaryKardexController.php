<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\TemporaryKardex;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for Kardex Model
|--------------------------------------------------------------------------
*/

class TemporaryKardexController extends Controller {

	protected $inventoriesConfig;


	public function index()
	{
		$kardexs = TemporaryKardex::warehouse()->get();
		return $kardexs;
	}


	public function store($product)
	{


	}

	public function save($product)
	{
		$date = new DateTime();
		$newKardex = [
			'productCode' => $product['code'],
			'productName' => $product['name'],
			'valuationMethod' => $product['stocktaking']['valuationMethod'],
			'date' => $date->format('Y-m-d H:i:s'),
			'concept' => 'Inventario Inicial',
			'balances' => [
				'quantity' => $product['stock'],
				'unitCost' => $product['unitCost'],
				'total' => $product['unitCost'] * $product['stock']
				]
		];

		TemporaryKardex::create($newKardex);

	}


	public function searchByParameters()
  {
    $params = Input::all();
    $startDate = (isset($params['startDate']) ? $params['startDate'] : '');
    $endDate = (isset($params['endDate']) ? $params['endDate'] : '');
    $productCodes = (isset($params['selectedProducts']) ? $params['selectedProducts'] : []);

    $kardexs = [];

    if ($startDate !== '' && $endDate !== '') {
      $kardexs = TemporaryKardex::warehouse()
      	->whereBetween('date', [new \MongoDate(strtotime($startDate.' 00:00:00')), new \MongoDate(strtotime($endDate.' 23:59:59'))])
        ->where(function($query) use($productCodes){
          if(count($productCodes) > 0){
            $query->whereRaw(array('productCode' => array('$in' => $productCodes)));
          }
        })
        ->with('product')
        ->get();
    }    

    return $kardexs;
  }


}
