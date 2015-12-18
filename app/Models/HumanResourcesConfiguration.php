<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class HumanResourcesConfiguration extends Moloquent {

  protected $collection = 'HumanResourcesConfiguration';
  protected $guarded = [];

  protected $casts = [
    'basicSalary' => 'float',
    'percentageUtilitiesWorkers' => 'float',
    'percentageUtilitiesBurden' => 'float',
    'whortySalary' => 'float',
    'liquidationType' => 'string'
  ];


  public function  __construct(array $attributes = [])
  {
    $userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    parent::__construct($attributes);
  }


}