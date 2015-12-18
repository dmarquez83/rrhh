<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Module extends Moloquent{

    protected $collection = 'Module';
    protected $guarded = array();
  
}
