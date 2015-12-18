 <?php

 use App\Models\MaritalStatus;
 use Illuminate\Database\Seeder;
 use Illuminate\Support\Facades\DB;

 class MaritalStatusSeeder extends Seeder{

  public function run(){
    DB::collection('MaritalStatus')->delete();

    MaritalStatus::create(array(
      'code' => 'S', 
      'name' => 'Soltero'
    ));

    MaritalStatus::create(array(
      'code' => 'C', 
      'name' => 'Casado'
    ));

    MaritalStatus::create(array(
      'code' => 'D', 
      'name' => 'Divorciado'
    ));

    MaritalStatus::create(array(
      'code' => 'U', 
      'name' => 'Unión Libre'
    ));

    MaritalStatus::create(array(
      'code' => 'V', 
      'name' => 'Viudo'
    ));

  }

}

