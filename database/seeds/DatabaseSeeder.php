<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	public function run()
	{
		Model::unguard();
		$this->call('CompanySeeder');
		$this->call('CompanyInfoSeeder');
		$this->call('WarehouseSeeder');
		$this->call('ModuleSeeder');
		$this->call('RoleSeeder');
		$this->call('UserSeeder');
		$this->call('DocumentConfigurationSeeder');
		$this->call('PaymentSeeder');
		$this->call('ProvinceSeeder');
		$this->call('MaritalStatusSeeder');
		$this->call('BankSeeder');
		$this->call('GeneralParameterSeeder');
		$this->call('BonusSeeder');
		$this->call('OfficeSeeder');
		$this->call('DepartmentsSeeder');
		$this->call('DiscountsSeeder');
		$this->call('EmployeesSeeder');
	  $this->call('PaymentConditionsSeeder');
	  $this->call('PaymentWaysSeeder');
	  $this->call('PaymentMethodsSeeder');
  }

}
