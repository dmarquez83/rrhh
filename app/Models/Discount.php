<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Discount extends Moloquent{

  protected $collection = 'Discounts';
  protected $guarded = array();

  public function  __construct(array $attributes = [])
  {
    $userInfo = Session::get('userInformation');
    $currentWarehouse = Session::get('currentWarehouse');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    $this->attributes['warehouse_id'] = $currentWarehouse['_id'];
    parent::__construct($attributes);
  }

  public function scopeWarehouse($query)
  {
    $currentWarehouse = Session::get('currentWarehouse');
    return $query->where('warehouse_id', '=', $currentWarehouse['_id']);
  }

}
