<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Warehouse extends Moloquent{

  protected $collection = 'Warehouses';
  protected $guarded = [];

  public function  __construct(array $attributes = [])
  {
    $userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];

    parent::__construct($attributes);
  }

}
