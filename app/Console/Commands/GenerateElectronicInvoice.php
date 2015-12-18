<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Helpers\ElectronicInvoice;

class GenerateElectronicInvoice extends Command {


	protected $name = 'generateElectronicInvoice';
	protected $description = 'Generando factura electronica';


	public function __construct()
	{
		parent::__construct();
	}

	public function fire()
	{
		$customerInvoiceNumber = $this->argument('number');
		$warehouseId = $this->argument('warehouse_id');
		ElectronicInvoice::make($customerInvoiceNumber, $warehouseId);
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
