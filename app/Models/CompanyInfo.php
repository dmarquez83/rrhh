<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class CompanyInfo extends Moloquent{

  protected $collection = 'CompanyInfo';
  protected $guarded = array();

}
