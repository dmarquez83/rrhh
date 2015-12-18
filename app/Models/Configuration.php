<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Configuration extends Moloquent{

  protected $collection = 'Configuration';
  protected $guarded = array();

}
