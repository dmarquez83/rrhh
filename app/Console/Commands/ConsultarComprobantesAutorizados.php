<?php namespace App\Console\Commands;

use App\Helpers\BarcodeGenerator;
use App\Models\PurchaseRetention;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\CompanyInfo;
use App\Models\ElectronicDocument;
use App\Models\CustomerInvoice;
use App\Helpers\ValidateElectronicDocument;
use Illuminate\Support\Facades\File;
use App\Helpers\SystemConfiguration;

class ConsultarComprobantesAutorizados extends Command {

	private $invocieXmlDirectoryPath;
	private $companyInfo;
	private $document;
  private $documentType;
	private $comprobantes;
	private $autorizeInvoiceXmlFullPath;
	private $numeroDeAutorizacion;
	private $fileName;
	protected $name = 'consultarComprobantesAutorizados';
	protected $description = 'Obtiende todos los comprobantes que han sido enviados al sri';

	public function __construct()
	{
		$this->companyInfo = CompanyInfo::first();
		$this->invocieXmlDirectoryPath = SystemConfiguration::getElectronicDocumentsPath(true);
		$this->retentionXmlDirectoryPath = SystemConfiguration::getElectronicDocumentsPath(true);
		parent::__construct();
	}


	public function fire()
	{
		$this->getComprobantesEnviados();
		$this->getRespuestaComprobante();
	}

	private function getComprobantesEnviados()
	{
		$this->comprobantes = ElectronicDocument::where('status', '=', 'RECIBIDO')->get();
	}

	private function getRespuestaComprobante()
  {
		foreach ($this->comprobantes as $key => $comprobante) {

			$this->document = $this->getDocument($comprobante['documentNumber'], $comprobante['documentType']);
      $this->documentType = $comprobante['documentType'];
			$this->ambiente = $comprobante['environment'];
			$this->emision = $comprobante['emission'];

      if ($this->documentType == 'CustomerInvoice'){
        $this->getRazonSocialCliente();
      }
      if ($this->documentType == 'PurchaseRetention'){
        $this->getRazonSocialProveedor();
      }

			try {
	        $time_start = microtime(true);
	        $soapClient = new \SoapClient(
	          'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl',
	          ['trace' => 1, 'exceptions'=> 1, 'connection_timeout'=> 15]
	        );
	        $soapClient->autorizacionComprobante(['claveAccesoComprobante' => $comprobante['accessPassword']])->RespuestaAutorizacionComprobante;
					$XmlRespuesta = $soapClient->__getLastResponse();
					$this->chequearErrores($XmlRespuesta, $comprobante);

	    } catch (\SoapFault $e) {
	        $time_request = (microtime(true) - $time_start);
	        if(ini_get('default_socket_timeout') < $time_request) {
	          ValidateElectronicDocument::setTimeoutError($comprobante['documentNumber'], $comprobante['documentType'], $comprobante['accessPassword']);
	        } else {
	          ValidateElectronicDocument::setCustomError($comprobante['documentNumber'], $comprobante['documentType'], $comprobante['accessPassword']);
	        }
	    }

		}

  }

	private function getDocument($documentNumber, $type)
  {
    switch ($type) {
      case 'CustomerInvoice':
        return CustomerInvoice::where('number', '=', $documentNumber)->first();
        break;
      case 'PurchaseRetention':
        return PurchaseRetention::where('number', '=', $documentNumber)->first();
        break;
    }
  }


	private function chequearErrores($xmlRespuesta, $comprobante){

    $respuestaAutorizacionComprobante = $this->leerXmlRespuesta($xmlRespuesta);
    $estado = $respuestaAutorizacionComprobante->autorizaciones->autorizacion->estado;

    if($estado == 'NO AUTORIZADO'){
      ValidateElectronicDocument::setUnauthorized($comprobante, $respuestaAutorizacionComprobante);
    } else if ($estado == 'AUTORIZADO'){
      $this->getNumeroAutorizacion($xmlRespuesta, $comprobante);
			$this->saveDocumentoAutorizado($xmlRespuesta, $comprobante['documentType']);
    }
  }

	private function leerXmlRespuesta($XmlRespuesta)
	{
		$xml = simplexml_load_string($XmlRespuesta);
		$ns = $xml->getNamespaces(true);
		$xmlAutorizationResponse = $xml->children($ns['soap'])->Body->children($ns['ns2'])->children()->children();
		return $xmlAutorizationResponse;
	}

	private function getNumeroAutorizacion($xmlRespuesta, $comprobante)
  {
    $respuestaAutorizacionComprobante = $this->leerXmlRespuesta($xmlRespuesta);
    $numeroDeAutorizacion = "".$respuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
    $this->$numeroDeAutorizacion = $numeroDeAutorizacion;
    if ($numeroDeAutorizacion != '' && $numeroDeAutorizacion != null) {
      ValidateElectronicDocument::setAuthorized($comprobante, $respuestaAutorizacionComprobante, $numeroDeAutorizacion);
      $this->generarRIDE($comprobante['accessPassword'], $numeroDeAutorizacion);
    }

  }

	private function generarRIDE($claveAccesso, $numeroDeAutorizacion)
	{
		if ($this->documentType === 'CustomerInvoice') {
			$data = [
					'companyInfo' => $this->companyInfo,
					'document' => $this->document,
					'claveAcceso' => $claveAccesso,
					'generator' => new \Picqer\Barcode\BarcodeGeneratorPNG(),
					'numeroAutorizacion' => $numeroDeAutorizacion,
					'customer' => $this->document['customer'],
					'products' => $this->document['products'],
					'companyName' => $this->companyName,
					'ambiente' => $this->ambiente,
					'emision' => $this->emision
			];
			$pdf = \PDF::loadView('pdf.ride', $data)->setPaper('a4');
			$pdf->save($this->invocieXmlDirectoryPath.$this->fileName."_ride.pdf");
			$this->enviarCorreo();
		}

		if ($this->documentType === 'PurchaseRetention') {
			$data = [
					'companyInfo' => $this->companyInfo,
					'document' => $this->document,
					'claveAcceso' => $claveAccesso,
					'generator' => new \Picqer\Barcode\BarcodeGeneratorPNG(),
					'numeroAutorizacion' => $numeroDeAutorizacion,
					'supplier' => $this->document['supplierInvoice']['supplier'],
          'supplierInvoice' => $this->document['supplierInvoice'],
					'taxes' => $this->document['taxes'],
					'companyName' => $this->companyName,
					'ambiente' => $this->ambiente,
					'emision' => $this->emision
			];
			$pdf = \PDF::loadView('pdf.retention', $data)->setPaper('a4');
			$pdf->save($this->retentionXmlDirectoryPath.$this->fileName."_ride.pdf");
			$this->enviarCorreo();
		}

	}

	private function enviarCorreo()
	{
		$emailTo = '';
		if ($this->documentType === 'CustomerInvoice') {
			$emailTo = $this->document['customer']['emails'][0];
		}
		if ($this->documentType === 'PurchaseRetention') {
			$emailTo = $this->document['supplierInvoice']['supplier']['emails'][0];
		}

		$companyName = $this->companyName;
		$data = [
			'documentNumber' => $this->document['number'],
			'companyName' => $this->companyName
		];

		\Mail::send('emails.electronicDocument', $data,
			function ($message) use ($emailTo, $companyName) {

				$message->to($emailTo, $companyName)->subject('Has recibido un(a) Documento Electrónico nuevo');
				$message->attach($this->autorizeInvoiceXmlFullPath, ['mime' => 'application/xml']);
				if ($this->documentType === 'CustomerInvoice') {
					$message->attach($this->invocieXmlDirectoryPath . $this->fileName . "_ride.pdf", ['mime' => 'application/pdf']);
				}
				if ($this->documentType === 'PurchaseRetention') {
					$message->attach($this->retentionXmlDirectoryPath . $this->fileName . "_ride.pdf", ['mime' => 'application/pdf']);
				}
		});

	}

	private function saveDocumentoAutorizado($XmlRespuesta, $type)
	{
		$fileName = '';
		$fullPath = '';
		switch ($type) {
			case 'CustomerInvoice':
				$fechaDocumento = new \DateTime($this->document['creationDate']);
				$fechaFormateada = $fechaDocumento->format('dmY_His');
				$fileName = 'factura_'.$fechaFormateada;
				$fullPath = $fileName.'_autorizado.xml';

				$this->fileName = $fileName;
				$this->autorizeInvoiceXmlFullPath = $this->invocieXmlDirectoryPath.$fullPath;
				File::put($this->autorizeInvoiceXmlFullPath, $XmlRespuesta);

				break;
			case 'PurchaseRetention':
				$fechaDocumento = new \DateTime($this->document['creationDate']);
				$fechaFormateada = $fechaDocumento->format('dmY_His');
				$fileName = 'retencion_'.$fechaFormateada;
				$fullPath = $fileName.'_autorizado.xml';

				$this->fileName = $fileName;
				$this->autorizeInvoiceXmlFullPath = $this->retentionXmlDirectoryPath.$fullPath;
				File::put($this->autorizeInvoiceXmlFullPath, $XmlRespuesta);
				break;
		}

	}

	private function getRazonSocialCliente()
  {
		$names = '';
    $surnames = '';
    if (isset($this->document['customer']['names'])) {
      $names = strtoupper($this->document['customer']['names']);
    }
    if (isset($this->document['customer']['surnames'])) {
      $surnames = strtoupper($this->document['customer']['surnames']);
    }
    $companyName = strtoupper($names." ".$surnames);
    $this->companyName = $companyName;
    if (isset($this->document['customer']['comercialName'])){
      $companyName = $this->document['customer']['comercialName'];
      $this->companyName = $companyName;
      return strtoupper($companyName);
    }
    if (isset($this->document['customer']['bussinessName'])){
      $companyName = $this->document['customer']['comercialName'];
      $this->companyName = $companyName;
      return strtoupper($companyName);
    }

    return $companyName;
  }

  private function getRazonSocialProveedor()
  {
		$names = '';
    $surnames = '';
    if (isset($this->document['supplierInvoice']['supplier']['names'])) {
      $names = strtoupper($this->document['supplierInvoice']['supplier']['names']);
    }
    if (isset($this->document['supplierInvoice']['supplier']['surnames'])) {
      $surnames = strtoupper($this->document['supplierInvoice']['supplier']['surnames']);
    }
    $companyName = strtoupper($names." ".$surnames);
    $this->companyName = $companyName;
    if (isset($this->document['supplierInvoice']['supplier']['comercialName'])){
      $companyName = $this->document['supplierInvoice']['supplier']['comercialName'];
      $this->companyName = $companyName;
      return strtoupper($companyName);
    }
    if (isset($this->document['supplierInvoice']['supplier']['bussinessName'])){
      $companyName = $this->document['supplierInvoice']['supplier']['bussinessName'];
      $this->companyName = $companyName;
      return strtoupper($companyName);
    }

    return $companyName;
  }


	protected function getArguments()
	{
		return [
			['example', InputArgument::OPTIONAL, 'An example argument.'],
		];
	}


	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
