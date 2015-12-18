<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Helpers\ElectronicRetention;

class GenerateElectronicRetention extends Command {


	protected $name = 'generateElectronicRetention';
	protected $description = 'Generando retención electronica';


	public function __construct()
	{
		parent::__construct();
	}

	public function fire()
	{
		$purchaseRetentionNumber = $this->argument('number');
		$warehouseId = $this->argument('warehouse_id');
		ElectronicRetention::make($purchaseRetentionNumber, $warehouseId);
	}

	protected function getArguments()
	{
		return [
			['number', InputArgument::REQUIRED, 'Número de comprobante'],
			['warehouse_id', InputArgument::REQUIRED, 'Id de la bodega'],
		];
	}

	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
