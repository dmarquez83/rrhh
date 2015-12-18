<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Province extends Moloquent{

  protected $collection = 'Province';
  protected $guarded = array();

}
