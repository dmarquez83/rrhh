<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class GeneralParameter extends Moloquent{

  protected $collection = 'GeneralParameters';
  protected $guarded = array();

  public function  __construct(array $attributes = [])
  {
  	$userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    parent::__construct($attributes);
  }

}