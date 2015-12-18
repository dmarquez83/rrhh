<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Payment extends Moloquent{

  protected $collection = 'Payment';
  protected $guarded = array();

}
