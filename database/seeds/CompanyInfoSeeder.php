<?php

use App\Models\CompanyInfo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyInfoSeeder extends Seeder  {

  public function run() {
    DB::collection('CompanyInfo')->delete();

    CompanyInfo::create([
      "identification"  => '1715339444001',
      "companyCode"  => '001',
      "businessName"  => "Demo"
    ]);

  }

}
