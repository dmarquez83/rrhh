<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class MaritalStatus extends Moloquent{

  protected $collection = 'MaritalStatus';
  protected $guarded = [];

}
